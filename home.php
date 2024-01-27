<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <title>Home Page</title>
</head>
<body>
    <div class="container home">
        <h1>Welcome to Home Page</h1>
        <?php
        session_start();

        // Check if the user is logged in
        if (isset($_SESSION['user']) && $_SESSION['user'] == "yes") {
            // Retrieve user information
            $email = $_SESSION['email'];

            // Display non-sensitive information (e.g., email)
            echo "<p>User Email: $email</p>";
        } else {
            // Redirect to the login page if the user is not logged in
            header("Location: login.php");
            exit();
        }
        ?>
        <a href="logout.php" class="btn btn-warning">Logout</a>
        <a href="change-password.php" class="btn btn-warning">Change Password</a>
    </div>
</body>
</html>
