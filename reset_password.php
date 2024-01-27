<?php
session_start();
require_once "database.php";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirmNewPassword = mysqli_real_escape_string($conn, $_POST['confirm_new_password']);

    // Password length check
    if (strlen($newPassword) < 8) {
        $errors['password'][] = "Password must be at least 8 characters long";
    }

    // Password complexity check
    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#])[A-Za-z\d@#]{8,}$/", $newPassword)) {
        $errors['password'] = [];

        if (!preg_match("/[a-z]/", $newPassword)) {
            $errors['password'][] = "Need to add a lowercase letter (a-z)";
        }

        if (!preg_match("/[A-Z]/", $newPassword)) {
            $errors['password'][] = "Need to add an uppercase letter (A-Z)";
        }

        if (!preg_match("/\d/", $newPassword)) {
            $errors['password'][] = "Need to add a digit (0-9)";
        }

        if (!preg_match("/[@#]/", $newPassword)) {
            $errors['password'][] = "Need to add a special character (@ or #)";
        }

        if (empty($errors['password'])) {
            $errors['password'][] = "Password must be at least 8 characters long";
        }
    }

    // Password match check
    if ($newPassword !== $confirmNewPassword) {
        $errors['password1'] = "Password does not match";
    }

    // Check if the new password is the same as the old password
    $validationCode = $_SESSION['validation_code'];
    $oldPasswordQuery = "SELECT password FROM users WHERE validation_code = ?";
    $stmtOldPassword = mysqli_prepare($conn, $oldPasswordQuery);
    mysqli_stmt_bind_param($stmtOldPassword, "s", $validationCode);
    mysqli_stmt_execute($stmtOldPassword);
    mysqli_stmt_bind_result($stmtOldPassword, $oldPassword);
    mysqli_stmt_fetch($stmtOldPassword);
    mysqli_stmt_close($stmtOldPassword);

    if (password_verify($newPassword, $oldPassword)) {
        $errors['password'][] = "New password should be different from the current password";
    }

    // If there are no errors, proceed to update the password
    if (empty($errors)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET password = ? WHERE validation_code = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $hashedPassword, $validationCode);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: login.php");
            exit;
        } else {
            $errors['general'] = "Error updating password. Please try again.";
        }

        mysqli_stmt_close($stmt);
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <form action="reset_password.php" method="post">
            <h2 style="text-align: center;">Reset Password</h2>
            <br>
            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $errors['general']; ?>
                </div>
            <?php endif; ?>
            <div class="form-group position-relative">
                <input type="password" class="form-control" name="new_password" placeholder="New Password:" value="<?php echo isset($_POST['new_password']) ? htmlspecialchars($_POST['new_password']) : ''; ?>">
                <button type="button" class="btn toggle-password position-absolute end-0 top-0">
                <i class="bi bi-eye-slash"></i>
                </button>
                <?php if (!empty($errors['password'])): ?>
                    <?php foreach ($errors['password'] as $error): ?>
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <?php echo $error; ?>
                            <button type='button' class='btn-close' data-dismiss='alert' aria-label='Close'></button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="form-group position-relative">
                <input type="password" class="form-control" name="confirm_new_password" placeholder="Confirm Password:" value="<?php echo isset($_POST['confirm_new_password']) ? htmlspecialchars($_POST['confirm_new_password']) : ''; ?>">
                <button type="button" class="btn toggle-password position-absolute end-0 top-0">
                <i class="bi bi-eye-slash"></i>
                </button>
                <?php if (!empty($errors['password1'])): ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <?php echo $errors['password1']; ?>
                        <button type='button' class='btn-close' data-dismiss='alert' aria-label='Close'></button>
                    </div>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Change Password</button>
        </form>
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
    </div>
    <script>
    $(document).ready(function () {
        // Show/hide password functionality
        $(".toggle-password").click(function () {
            var input = $(this).prev("input");
            var icon = $(this).find("i");

            if (input.attr("type") === "password") {
                input.attr("type", "text");
                icon.removeClass("bi bi-eye-slash").addClass("bi-eye");
            } else {
                input.attr("type", "password");
                icon.removeClass("bi-eye").addClass("bi bi-eye-slash");
            }
        });

        // Adjust the height of the password input
        $(".form-group-password").on("click", ".toggle-password", function () {
            var input = $(this).prev("input");
            var height = input.height();

            if (input.attr("type") === "password") {
                input.css("height", height + "px");
            } else {
                input.css("height", "");
            }
        });
    });
</script>
</body>
</html>

