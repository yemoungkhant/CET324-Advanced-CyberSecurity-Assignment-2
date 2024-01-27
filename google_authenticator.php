<?php
declare(strict_types=1);

require 'vendor/autoload.php';

$secret = 'XVQ2UIGO75XRUKJO';

$link = \Sonata\GoogleAuthenticator\GoogleQrUrl::generate('Authentication', $secret, 'QR Generation');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Authentication</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <?php
        if(isset($_POST['submit'])) {
            $code = $_POST['code'];
            $g = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();
        
            if ($g->checkCode($secret, $code)) {
                session_start();
                header("Location: home.php");
                die();
            } 
            else {
                echo "<div class = 'alert alert-danger'> Incorrect Authentication Code!</div>";
            }
            }
        ?>
    <div class="container">
        <form action="google_authenticator.php" method="post">
            <h2 style="text-align: center;">Google Authentication</h2>
            <br>
            <p></p>
            <center><img src="<?= $link;?>"></center>
            <br>
            <div class="form-group">
                <input type="text" class="form-control" name="code" placeholder="Authentication Code:" required>
            </div>
            <center><button type="submit" class="btn btn-primary" name="submit">Continue</button></center>
        </form>
        <br>
        <div class="return">
            <a href="login.php">Return to Email</a></p>
    </div>
    </div>
    </div>
</body>
</html>
