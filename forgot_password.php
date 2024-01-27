<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
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

        $email = $user['email']; // Update the email variable with the user's email from the database

        // Check the number of password changes in the last 24 hours
        $today = date('Y-m-d');
        $sql_count_changes = "SELECT COUNT(*) AS change_count FROM password_changes WHERE email = ? AND change_date >= ?";
        $stmt_count_changes = mysqli_prepare($conn, $sql_count_changes);
        mysqli_stmt_bind_param($stmt_count_changes, "ss", $email, $today);
        mysqli_stmt_execute($stmt_count_changes);
        $result_count_changes = mysqli_stmt_get_result($stmt_count_changes);

        if ($result_count_changes && $row_count_changes = mysqli_fetch_assoc($result_count_changes)) {
            $change_count = $row_count_changes['change_count'];

            // Allow only two password changes in one day
            if ($change_count >= 2) {
                echo "<div class='alert alert-danger'>You can only change your password twice in one day.</div>";
                header("refresh:3;url=login.php"); 
                exit();
            }
        }
        
        $validationCode = generateValidationCode();
        $_SESSION['validation_code'] = $validationCode;
        $updateSql = "UPDATE users SET validation_code = ? WHERE email = ?";
       
        $updateStmt = mysqli_prepare($conn, $updateSql);
        mysqli_stmt_bind_param($updateStmt, "ss", $validationCode, $email);
        mysqli_stmt_execute($updateStmt);

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'luminkhant056@gmail.com';
            $mail->Password = 'lxdu ptih rqtz kfpb';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('luminkhant056@gmai.com', 'Password Reset');
            $mail->addAddress($email);

            $mail->Subject = 'Password Reset';
            $mail->Body = "Your validation code for password reset is: $validationCode";

            $mail->send();

            echo "<div class='alert alert-success'>Validation code sent successfully. Check your email.</div>";

            header("refresh:3;url=validation.php");
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
        <form action="forgot_password.php" method="post">
            <h2 style="text-align: center;">Forgot Password</h2>
            <br>
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email:">
            </div>
            <button type="submit" class="btn btn-primary">Send Validation Code</button>
        </form>
        <br>
        <div class="return">
            <a href="login.php">Return to Login</a></p>
        </div>
    </div>
    </div>
</body>
</html>
