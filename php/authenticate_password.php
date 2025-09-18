<?php
session_start();
include 'database/connection.php';

// Check if authentication is needed and user session is set
if (!isset($_SESSION['auth_needed']) || !isset($_SESSION['Login_ID_for_verification'])) {
    $_SESSION['login_error'] = "Invalid session. Please log in again.";
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['otp1'], $_POST['otp2'], $_POST['otp3'], $_POST['otp4'], $_POST['otp5'], $_POST['otp6'])) {
    // Combine the 6 OTP inputs into a single string
    $input_otp = $_POST['otp1'] . $_POST['otp2'] . $_POST['otp3'] . $_POST['otp4'] . $_POST['otp5'] . $_POST['otp6'];
    $login_id = $_SESSION['Login_ID_for_verification'];

    // Query to check the provided OTP
    $query = "SELECT * FROM accounts WHERE Login_ID = '$login_id' AND auth_code = '$input_otp'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // OTP is correct, verify the account
        $update_status = "UPDATE accounts SET is_verified = 1 WHERE Login_ID = '$login_id'";
        mysqli_query($conn, $update_status);

        // Get user details for the main session
        $row = mysqli_fetch_assoc($result);
        $_SESSION['Login_ID'] = $row['Login_ID'];
        $_SESSION['Account_Type'] = $row['Account_Type'];
        $_SESSION['name'] = $row['name'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['is_verified'] = 1;
        
        // Unset temporary session variables
        unset($_SESSION['auth_needed']);
        unset($_SESSION['email_for_verification']);
        unset($_SESSION['Login_ID_for_verification']);

        // Redirect to a dashboard or a success page
        switch ($row['Account_Type']) {
            case 1:
                header("Location: ../admin_dashboard.php");
                break;
            case 2:
                header("Location: ../department_dashboard.php");
                break;
            case 3:
                header("Location: ../financial_officer_dashboard.php");
                break;
            default:
                // Fallback for an unknown account type
                header("Location: ../login.php");
                break;
        }
        exit();

    } else {
        // Incorrect OTP, set an error message and redirect back
        $_SESSION['login_error'] = "Invalid OTP. Please try again.";
        // The key is to NOT unset the session variables here
        header("Location: ../login.php");
        exit();
    }
} else {
    // If accessed directly or without full OTP, redirect back to login
    header("Location: ../login.php");
    exit();
}
?>