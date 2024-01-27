<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="login.css">
    <!-- recaptcha -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script> 
</head>
<body>
    <div class="container">
        <div class="container">
            <h1 class="name">Login</h1>
            <form action="login.php" method="post">
                <div class="form-group">
                    <input type="email" placeholder="Enter Email:" name="email" class="form-control" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
                    <?php
                    if (isset($_POST["login"])) {
                        $email = $_POST["email"];
                        $password = $_POST["password"];
                        require_once "database.php";
                        $sql = "SELECT * FROM users WHERE email = '$email'";
                        $result = mysqli_query($conn, $sql);
                        $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
                        if (!$user) {
                            echo "<div class='alert alert-danger alert-dismissible'>
                                    <button type='button' class='btn-close' data-dismiss='alert' aria-label='Close'></button>
                                    Email does not match
                                  </div>";
                        }
                    }
                    echo '<script>
                    $(document).ready(function(){
                        $(".alert").alert();
                        $(".alert .btn-close").on("click", function(){
                            $(this).closest(".alert").fadeOut("slow", function(){
                                $(this).remove();
                            });
                        });
                    });
                    </script>';
                    ?>
                </div>
                <div class="form-group-1 position-relative">
                    <input type="password" placeholder="Enter Password:" name="password" class="form-control" value="<?php echo isset($_POST['password']) ? $_POST['password'] : ''; ?>">
                    <button type="button" class="btn toggle-password position-absolute end-0 top-0">
                    <i class="bi bi-eye-slash"></i>
                    </button>
                    <?php
                    if (isset($_POST["login"]) && $user) {
                        if (password_verify($password, $user["password"])) {
                            // Successful login
                            session_start();
                            $_SESSION["user"] = "yes";
                            $_SESSION["email"] = $user["email"];
                            header("Location: OTP_login.php");
                            die();
                        } else {
                            // Incorrect password
                            echo "<div class='alert alert-danger alert-dismissible'>
                                    <button type='button' class='btn-close' data-dismiss='alert' aria-label='Close'>
                                    </button>
                                    Password does not match
                                  </div>";
                        }
                    }
                    echo '<script>
                    $(document).ready(function(){
                        $(".alert").alert();
                        $(".alert .btn-close").on("click", function(){
                            $(this).closest(".alert").fadeOut("slow", function(){
                                $(this).remove();
                            });
                        });
                    });
                    </script>';
                    ?>
                </div>
                <div class="forgot"><a href="forgot_Password.php">Forgot Password?</a></div>
                <!-- //// -->
                <!-- <div class="g-recaptcha" data-sitekey="6Lde6lIpAAAAAKRucvLvNpae11N-4-Ym5ouD7uz7"></div>
                <?php if (!empty($errors['recaptcha'])): ?>
                <div class="alert alert-warning"><?= $errors['recaptcha']; ?></div>
                <?php endif; ?>  -->
                <!-- ///// -->
                <div class="form-btn-login">
                    <input type="submit" value="Login" name="login" class="btn btn-primary">
                </div>
            </form>
            <div><p>Not registered yet <a href="registration.php">Register Here</a></p></div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofpXaPpu7uq1C4u6EU7EQj5gFIdl+8bGo" crossorigin="anonymous"></script>
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
