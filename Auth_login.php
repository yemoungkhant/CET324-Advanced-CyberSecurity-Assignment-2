<?php
session_start();
require_once "database.php";

$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enteredCode = mysqli_real_escape_string($conn, $_POST['OTP_code']);
    $storedCode = $_SESSION['OTP_code'];

    if ($enteredCode == $storedCode) {
        $sql = "SELECT email FROM users WHERE OTP_code = ?";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $enteredCode);
        mysqli_stmt_execute($stmt);
        
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user) {
            $_SESSION["OTP_verified"] = true;
            header("Location: google_authenticator.php");
            exit;
        } else {
            $errorMessage = "Invalid validation code. Please try again.";
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    } else {
        if (empty($enteredCode)) {
            $errorMessage = "Validation code cannot be empty.";
        } elseif (strlen($enteredCode) < 6) {
            $errorMessage = "Validation code should be 6 characters long.";
        } elseif (strlen($enteredCode) > 6 ) {
            $errorMessage = "Validation code should be 6 characters long.";
        } else {
            $errorMessage = "Validation code does not match. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auth_login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <form action="Auth_login.php" method="post">
            <h2 style="text-align: center;">OTP Code</h2>
            <br>
            <div class="form-group">
                <input type="text" class="form-control" name="OTP_code" placeholder="Validation Code:">
                <?php if (!empty($errorMessage)) { ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <?php echo $errorMessage; ?>
                        <button type='button' class='btn-close' data-dismiss='alert' aria-label='Close'></button>
                    </div>
                <?php } ?>
            </div>
            <button type="submit" class="btn btn-primary">Confirm</button>
        </form>
        <br>
        <div class="return">
            <a href="OTP_login.php">Return to Email</a></p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofpXaPpu7uq1C4u6EU7EQj5gFIdl+8bGo" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function(){
            $(".alert").alert();
            $(".alert .btn-close").on("click", function(){
                $(this).closest(".alert").fadeOut("slow", function(){
                    $(this).remove();
                });
            });
        });
    </script>
</body>
</html>
