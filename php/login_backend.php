<?php
session_start();
include 'database/connection.php'; // Your database connection

// Load PHPMailer classes
require 'c:/xampp/htdocs/financial/PHPMailer/src/Exception.php';
require 'c:/xampp/htdocs/financial/PHPMailer/src/PHPMailer.php';
require 'c:/xampp/htdocs/financial/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize user input
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $password = $_POST['password'];

    // Query to check for the user by User_ID
    $query = "SELECT * FROM accounts WHERE User_ID = '$user_id'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 0) {
        // User not found
        $_SESSION['login_error'] = "Account is not yet registered.";
        header("Location: ../login.php");
        exit();
    } else {
        $row = mysqli_fetch_assoc($result);

        // Verify the password
        if (password_verify($password, $row['password'])) {
            // Password is correct, generate and send OTP
            $new_auth_code = random_int(100000, 999999);

            // Update auth code in the database and set is_verified to 0
            $update_auth = "UPDATE accounts SET auth_code = '$new_auth_code', is_verified = 0 WHERE Login_ID = '{$row['Login_ID']}'";
            mysqli_query($conn, $update_auth);

            // Initialize PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'maginghotdogpano@gmail.com'; // Your Gmail address
                $mail->Password   = 'eefb cbip bkhp fskj';     // Your App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Recipients
                $mail->setFrom('4102Financial@gmail.com', '4102 Financial Ni Guzman');
                $mail->addAddress($row['email'], $row['name']);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Your Authentication Code';
                $mail->Body    = "Your authentication code is: <b>$new_auth_code</b>";

                $mail->send();
            } catch (Exception $e) {
                $_SESSION['login_error'] = "Error sending authentication email. Please try again.";
                header("Location: ../login.php");
                exit();
            }

            // Prepare session for verification
            $_SESSION['email_for_verification'] = $row['email'];
            $_SESSION['Login_ID_for_verification'] = $row['Login_ID'];
            $_SESSION['auth_needed'] = true;
            $_SESSION['temp_account_type'] = $row['Account_Type']; // Store account type temporarily

            // Redirect to the login page for OTP input
            header("Location: ../login.php");
            exit();
        } else {
            // Incorrect password
            $_SESSION['login_error'] = "User ID or password is incorrect.";
            header("Location: ../login.php");
            exit();
        }
    }
} else {
    // If accessed directly, redirect to login page
    header("Location: ../login.php");
    exit();
}
?>