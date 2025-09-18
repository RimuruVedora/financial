<?php
include 'database/connection.php';
// Report all errors, warnings, and notices
error_reporting(E_ALL);


header('Content-Type: application/json');
$response = ['success' => false, 'message' => '', 'ticket_entry' => null];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $collectionId = $data['collection_id'] ?? null;
    $action = $data['action'] ?? null;
    $reason = $data['reason'] ?? null;

    if (!$collectionId || !$action) {
        $response['message'] = 'Missing collection_id or action.';
        echo json_encode($response);
        exit();
    }

    $conn->begin_transaction();

    try {
        // 1. Fetch collection details, including any existing ticket_entry, balance, and amount
        $stmt_collection = $conn->prepare("SELECT BALANCE, ticket_entry, amount, OR_NO, LOCATION, date FROM collections WHERE collection_id = ?");
        $stmt_collection->bind_param("i", $collectionId);
        $stmt_collection->execute();
        $result_collection = $stmt_collection->get_result();
        
        if ($result_collection->num_rows === 0) {
            throw new Exception("Collection with the given ID not found.");
        }
        
        $collection_data = $result_collection->fetch_assoc();
        $currentCollectionBalance = $collection_data['BALANCE'];
        $existingTicketEntry = $collection_data['ticket_entry'];
        $newAmount = $collection_data['amount'];
        $or_no = $collection_data['OR_NO'];
        $location = $collection_data['LOCATION'];
        $collectionDate = $collection_data['date'];
        $stmt_collection->close();
        
        if ($action === 'approve') {
            $status = 'approve';
            $newBalance = $currentCollectionBalance - $newAmount;
            
            // Check if ticket_entry already exists in ticket_receivable
            $stmt_check_ticket = $conn->prepare("SELECT COUNT(*) FROM ticket_receivable WHERE ticket_entry = ?");
            $stmt_check_ticket->bind_param("s", $existingTicketEntry);
            $stmt_check_ticket->execute();
            $result_check = $stmt_check_ticket->get_result()->fetch_row();
            $isExistingTicket = ($result_check[0] > 0);
            $stmt_check_ticket->close();

            // 2. Update the status and balance in the `collections` table
            $stmt1 = $conn->prepare("UPDATE collections SET status = ?, BALANCE = ? WHERE collection_id = ?");
            $stmt1->bind_param("sii", $status, $newBalance, $collectionId);
            $stmt1->execute();
            if ($stmt1->affected_rows === 0) {
                throw new Exception("Failed to update collections status.");
            }
            $stmt1->close();
            
            if ($isExistingTicket) {
                // Handle subsequent payments
                
                // Fetch current balance and amount from ticket_receivable
                $stmt_fetch_current = $conn->prepare("SELECT balance, Amount, date_time FROM ticket_receivable WHERE ticket_entry = ?");
                $stmt_fetch_current->bind_param("s", $existingTicketEntry);
                $stmt_fetch_current->execute();
                $result_current = $stmt_fetch_current->get_result()->fetch_assoc();
                $currentReceivableBalance = $result_current['balance'];
                $currentReceivableAmount = $result_current['Amount'];
                $firstPaymentDate = new DateTime($result_current['date_time']);
                $stmt_fetch_current->close();

                // Calculate new total amount and new balance
                $updatedAmount = $currentReceivableAmount + $newAmount;
                $updatedBalance = $currentReceivableBalance - $newAmount;

                // Update the amount and balance in `ticket_receivable`
                $stmt_update_receivable = $conn->prepare("UPDATE ticket_receivable SET Amount = ?, balance = ? WHERE ticket_entry = ?");
                $stmt_update_receivable->bind_param("iis", $updatedAmount, $updatedBalance, $existingTicketEntry);
                $stmt_update_receivable->execute();
                if ($stmt_update_receivable->affected_rows === 0) {
                     throw new Exception("Failed to update ticket_receivable for existing ticket.");
                }
                $stmt_update_receivable->close();

                // Update aging bucket based on time since original entry
                $stmt_find_aging_id = $conn->prepare("SELECT aging_id FROM aging_bucket WHERE ticket_entry = ?");
                $stmt_find_aging_id->bind_param("s", $existingTicketEntry);
                $stmt_find_aging_id->execute();
                $result_aging = $stmt_find_aging_id->get_result()->fetch_assoc();
                $agingId = $result_aging['aging_id'];
                $stmt_find_aging_id->close();
                
                $today = new DateTime($collectionDate);
                $interval = $today->diff($firstPaymentDate);
                $daysDiff = $interval->days;

                $aging_column = '';
                if ($daysDiff <= 30) {
                    $aging_column = '30_Days';
                } else if ($daysDiff > 30 && $daysDiff <= 60) {
                    $aging_column = '60_Days';
                } else if ($daysDiff > 60) {
                    $aging_column = '90_Days';
                }
                
                if (!empty($aging_column)) {
                    $stmt_update_aging = $conn->prepare("UPDATE aging_bucket SET `$aging_column` = COALESCE(`$aging_column`, 0) + ? WHERE aging_id = ?");
                    $stmt_update_aging->bind_param("ii", $newAmount, $agingId);
                    $stmt_update_aging->execute();
                    if ($stmt_update_aging->affected_rows === 0) {
                        throw new Exception("Failed to update aging bucket for existing ticket entry.");
                    }
                    $stmt_update_aging->close();
                }

                $ticketEntry = $existingTicketEntry;
                
            } else {
                // Handle initial payment
                $ticketEntry = $existingTicketEntry;
                if ($ticketEntry === null || $ticketEntry === '') {
                    $stmt_get_last_ticket = $conn->prepare("SELECT MAX(ticket_entry) as last_ticket FROM collections");
                    $stmt_get_last_ticket->execute();
                    $result_last_ticket = $stmt_get_last_ticket->get_result();
                    $last_ticket_row = $result_last_ticket->fetch_assoc();
                    $lastTicket = $last_ticket_row['last_ticket'];
                    $stmt_get_last_ticket->close();
                    
                    $ticketEntry = ($lastTicket === null) ? 100000 : $lastTicket + 1;

                    $stmt_update_ticket = $conn->prepare("UPDATE collections SET ticket_entry = ? WHERE collection_id = ?");
                    $stmt_update_ticket->bind_param("ii", $ticketEntry, $collectionId);
                    $stmt_update_ticket->execute();
                    $stmt_update_ticket->close();
                }

                // 3. Insert data into `aging_bucket` first
                $stmt_aging = $conn->prepare("INSERT INTO aging_bucket (collection_id, ticket_entry, down_payment) VALUES (?, ?, ?)");
                $stmt_aging->bind_param("iii", $collectionId, $ticketEntry, $newAmount);
                $stmt_aging->execute();
                if ($stmt_aging->affected_rows === 0) {
                    throw new Exception("Failed to insert into aging_bucket.");
                }
                $agingId = $conn->insert_id;
                $stmt_aging->close();

                // 4. Then insert into `ticket_receivable`
                $stmt2 = $conn->prepare("INSERT INTO ticket_receivable (collection_id, amount, balance, ticket_entry, aging_id) VALUES (?, ?, ?, ?, ?)");
                $stmt2->bind_param("iiiii", $collectionId, $newAmount, $currentCollectionBalance, $ticketEntry, $agingId);
                $stmt2->execute();
                if ($stmt2->affected_rows === 0) {
                    throw new Exception("Failed to insert into ticket_receivable.");
                }
                $stmt2->close();
            }

            // Fetch the generated TICKET_ID
            $stmt_get_ticket_id = $conn->prepare("SELECT Ticket_ID FROM ticket_receivable WHERE ticket_Entry = ?");
            $stmt_get_ticket_id->bind_param("s", $ticketEntry);
            $stmt_get_ticket_id->execute();
            $result_ticket_id = $stmt_get_ticket_id->get_result();
            $ticketId_row = $result_ticket_id->fetch_assoc();
            
            if (!$ticketId_row) {
                 throw new Exception("Could not find Ticket_ID for the given ticket_Entry.");
            }
            
            $ticketId = $ticketId_row['Ticket_ID'];
            $stmt_get_ticket_id->close();

            // 5. Update revenue table
            $stmt_revenue = $conn->prepare("UPDATE revenue SET Revenue = Revenue + ? WHERE Revenue_ID = 1");
            $stmt_revenue->bind_param("i", $newAmount);
            $stmt_revenue->execute();
            if ($stmt_revenue->affected_rows === 0) {
                 throw new Exception("Failed to update revenue.");
            }
            $stmt_revenue->close();

            $status_acc = 'accept';
            $stmt3 = $conn->prepare("INSERT INTO account_receivables (Collection_ID, TICKET_ID, STATUS) VALUES (?, ?, ?)");
            $stmt3->bind_param("iis", $collectionId, $ticketId, $status_acc);
            $stmt3->execute();

            if ($stmt3->affected_rows === 0) {
                throw new Exception("Failed to insert into account_receivables.");
            }
            $stmt3->close();
        
            $response['success'] = true;
            $response['message'] = 'Action successful.';
            $response['ticket_entry'] = $ticketEntry;
            
        } else if ($action === 'reject') {
             if ($reason === null || trim($reason) === '') {
                 throw new Exception("Reason for rejection is required.");
             }
             $status = 'REJECTED';
             $stmt_reject = $conn->prepare("UPDATE collections SET status = ?, REASON = ? WHERE collection_id = ?");
             $stmt_reject->bind_param("ssi", $status, $reason, $collectionId);
             $stmt_reject->execute();
             if ($stmt_reject->affected_rows === 0) {
                 throw new Exception("Failed to update collections status to rejected.");
             }
             $stmt_reject->close();
             
             $response['success'] = true;
             $response['message'] = 'Rejection successful.';
        } else {
             throw new Exception("Invalid action.");
        }

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        $response['message'] = 'Transaction failed: ' . $e->getMessage();
        http_response_code(500);
    }

    $conn->close();
} else {
    $response['message'] = 'Invalid request method.';
    http_response_code(405);
}

echo json_encode($response);
?>