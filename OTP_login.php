<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login_OTP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
<?php
session_start();

require_once "database.php";
require_once "PHPMailer/src/PHPMailer.php";
require_once "PHPMailer/src/SMTP.php";
require_once "PHPMailer/src/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$updateStmt = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $sql = "SELECT * FROM users WHERE email = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        $OTPCode = generateValidationCode();
        $_SESSION['OTP_code'] = $OTPCode;
        $updateSql = "UPDATE users SET OTP_code = ? WHERE email = ?";
       
        $updateStmt = mysqli_prepare($conn, $updateSql);
        mysqli_stmt_bind_param($updateStmt, "ss", $OTPCode, $email);
        mysqli_stmt_execute($updateStmt);

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'luminkhant056@gmail.com';
            $mail->Password = 'fizd xqtg ejdd yekt';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('luminkhant056@gmai.com', 'Password Reset');
            $mail->addAddress($email);

            $mail->Subject = 'Password Reset';
            $mail->Body = "Your login OTP is here: $OTPCode";

            $mail->send();

            echo "<div class='alert alert-success'>OTP code sent successfully. Check your email.</div>";

            header("refresh:3;url=Auth_login.php");
            exit;
            
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Email delivery failed. </div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Email doesn't exist. Please try again.</div>";
    } 

    mysqli_stmt_close($stmt);
    if ($updateStmt) {
        mysqli_stmt_close($updateStmt);
    }
    mysqli_close($conn);
}

function generateValidationCode($length = 6) {
    return rand(pow(10, $length-1), pow(10, $length)-1);
}
?>
    <div class="container">
        <form action="OTP_login.php" method="post">
            <h2 style="text-align: center;">OTP for Login</h2>
            <br>
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email:">
            </div>
            <button type="submit" class="btn btn-primary">Send Validation Code</button>
        </form>
        <br>
        <div>
        <div class="return">
            <a href="login.php">Return to Login</a>
        </div>
        </div>
    </div>
</body>
</html>
