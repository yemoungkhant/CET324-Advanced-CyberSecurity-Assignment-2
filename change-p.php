<?php
session_start();

if (isset($_SESSION['user'])) {
    include "database.php";

    if (isset($_POST['op']) && isset($_POST['np']) && isset($_POST['c_np'])) {
        function validate($data)
        {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        $op = validate($_POST['op']);
        $np = validate($_POST['np']);
        $c_np = validate($_POST['c_np']);

        if (empty($op) || empty($np)) {
            header("Location: change-password.php?error=Invalid input");
            exit();
        }

        if ($np !== $c_np) {
            header("Location: change-password.php?error=Password does not match");
            exit();
        }

        if (strlen($np) < 8 || !preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/", $np)) {
            header("Location: change-password.php?error=New password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one special character and one digit");
            exit();
        }

        $email = $_SESSION['email'];

        // Check the number of password changes in the last 24 hours
        $today = date('Y-m-d');
        $sql_count_changes = "SELECT COUNT(*) AS change_count FROM password_changes WHERE email='$email' AND change_date >= '$today'";
        $result_count_changes = mysqli_query($conn, $sql_count_changes);

        if ($result_count_changes && $row_count_changes = mysqli_fetch_assoc($result_count_changes)) {
            $change_count = $row_count_changes['change_count'];

            // Allow only two password changes in one day
            if ($change_count >= 2) {
                header("Location: change-password.php?error=You can only change your password twice in one day");
                exit();
            }
        }

        // Update the password and record the change
        $sql = "SELECT email, password FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $sql);

        if ($result && $row = mysqli_fetch_assoc($result)) {
            $hashed_password = $row['password'];

            if (password_verify($op, $hashed_password)) {
                // Check if the new password is different from the old password
                if (password_verify($np, $hashed_password)) {
                    header("Location: change-password.php?error=New password should be different from the old password");
                    exit();
                }

                $hashed_np = password_hash($np, PASSWORD_DEFAULT);

                // Update the user's password
                $sql_update = "UPDATE users SET password='$hashed_np' WHERE email='$email'";
                mysqli_query($conn, $sql_update);

                // Record the password change
                $sql_record_change = "INSERT INTO password_changes (email, change_date) VALUES ('$email', NOW())";
                mysqli_query($conn, $sql_record_change);

                header("Location: change-password.php?success=Password changed successfully");
                exit();
            } else {
                header("Location: change-password.php?error=Incorrect old password");
                exit();
            }
        } else {
            header("Location: change-password.php?error=User not found");
            exit();
        }
    } else {
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>
