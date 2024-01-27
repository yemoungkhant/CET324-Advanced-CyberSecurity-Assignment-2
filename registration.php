<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="register.css">
    <!-- recaptcha -->
    <script src= 
        "https://www.google.com/recaptcha/api.js" async defer> 
    </script> 
</head>
<body>
    <div class="container">
        <?php
        $fullName = $email = $password = $passwordRepeat = $checkBox = '';
        $errors = array();

        if (isset($_POST["submit"])) {
            $fullName = $_POST["fullname"];
            $email = $_POST["email"];
            $password = $_POST["password"];
            $passwordRepeat = $_POST["repeat_password"];
            $checkBox = isset($_POST["checkbox"]) ? $_POST["checkbox"] : '';
            $recaptcha = $_POST['g-recaptcha-response']; 

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $secret_key = '6Lde6lIpAAAAAPgG_Jwm0xykewRgUlBe__ZwXqec';
            $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $recaptcha; 
            $response = file_get_contents($url); 
            $response = json_decode($response);
            
            // Validation for each field
            if (empty($fullName)) {
                $errors['fullname'] = "Full name is required";
            }

            if (empty($email)) { 
                $errors['email'] = "Email is required";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Email is not valid";
            }

            if (empty($password) || empty($passwordRepeat) || empty($checkBox)) {
                $errors['general'] = "All fields are required";
            }

            if (strlen($password) < 8) {
                $errors['password'] = "Password must be at least 8 characters long";
            }

            if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#])[A-Za-z\d@#]{8,}$/", $password)) {
                $errors['password'] = [];
            
                if (!preg_match("/[a-z]/", $password)) {
                    $errors['password'][] = "Need to add a lowercase letter (a-z)";
                }
            
                if (!preg_match("/[A-Z]/", $password)) {
                    $errors['password'][] = "Need to add an uppercase letter (A-Z)";
                }
            
                if (!preg_match("/\d/", $password)) {
                    $errors['password'][] = "Need to add a digit (0-9)";
                }
            
                if (!preg_match("/[@#]/", $password)) {
                    $errors['password'][] = "Need to add a special character (@ or #)";
                }
            
                if (empty($errors['password'])) {
                    $errors['password'][] = "Password must be at least 8 characters long";
                }
            }
            

            if ($password !== $passwordRepeat) {
                $errors['password1'] = "Password does not match";
            }

            if (empty($checkBox)) {
                $errors['checkbox'] = "You must agree to the terms and conditions";
            }

            if (!$response->success) { 
                $errors['recaptcha'] = "Need to verifiy reCAPTCHA";
            }

            require_once "database.php";
            $sql = "SELECT * FROM users WHERE email = '$email'";
            $result = mysqli_query($conn, $sql);
            $rowCount = mysqli_num_rows($result);
            if ($rowCount > 0) {
                $errors['email'] = "Email already exists!";
            }

            // Display the errors under the relevant form fields
            foreach ($errors as $key => $error) {
                if (!empty($error)) {
                    echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">';
                    if (is_array($error)) {
                        foreach ($error as $err) {
                            echo '<strong>Error!</strong> ' . $err . '<br>';
                        }
                    } else {
                        echo '<strong>Error!</strong> ' . $error;
                    }
                    echo '<button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
                          </div>';
                    break; // Display only the first error
                }
            }

            // Add the script to close alerts as before
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

            // If there are no errors, proceed with database insertion
            if (empty($errors['fullname']) && empty($errors['email']) && empty($errors['password']) && empty($errors['checkbox']) && empty($errors['password1']) && empty($errors['recaptcha'])) {
                $sql = "INSERT INTO users (full_name, email, password, checkbox) VALUES (?, ?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);
                $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
                if ($prepareStmt) {
                    mysqli_stmt_bind_param($stmt, "ssss", $fullName, $email, $passwordHash, $checkBox);
                    mysqli_stmt_execute($stmt);
                    echo '<div class="alert alert-success">You are registered successfully.</div>';
                    $fullName = $email = $password = $passwordRepeat = $checkBox = '';
                } else {
                    die("Something went wrong");
                }
            }
        }
        ?>
        <div class="container">
            <h1 class="name">Registration</h1>
            <form action="registration.php" method="post">
                <div class="form-group">
                    <input type="text" class="form-control" name="fullname" placeholder="Enter Your Name" value="<?= htmlspecialchars($fullName) ?>">
                    <?php if (!empty($errors['fullname'])): ?>
                        <div class="alert alert-warning"><?= $errors['fullname']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" name="email" placeholder="Enter Your Email:" value="<?= htmlspecialchars($email) ?>">
                    <?php if (!empty($errors['email'])): ?>
                        <div class="alert alert-warning"><?= $errors['email']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group position-relative">
                    <input type="password" class="form-control" name="password" placeholder="Password:" value="<?= htmlspecialchars($password) ?>">
                    <button type="button" class="btn toggle-password position-absolute end-0 top-0">
                    <i class="bi bi-eye-slash"></i>
                    </button>
                    <?php if (!empty($errors['password'])): ?>
                    <?php foreach ($errors['password'] as $error): ?>
                        <div class="alert alert-warning"><?= $error; ?></div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    <small class="font">
                       Password must be at least 8 characters long
                    </small>
                </div>
                <div class="form-group-regi position-relative">
                    <input type="password" class="form-control" name="repeat_password" placeholder="Confirm Password:" value="<?= htmlspecialchars($passwordRepeat) ?>">
                    <button type="button" class="btn toggle-password position-absolute end-0 top-0">
                    <i class="bi bi-eye-slash"></i>
                    </button>
                    <?php if (!empty($errors['password1'])): ?>
                        <div class="alert alert-warning"><?= $errors['password1']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-check-regi">
                    <input class="form-check-input" type="checkbox" value="1" id="flexCheckChecked" name="checkbox" <?= $checkBox == '1' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="flexCheckChecked">
                        Agree to the terms and conditions
                    </label>
                    <?php if (!empty($errors['checkbox'])): ?>
                        <div class="alert alert-warning"><?= $errors['checkbox']; ?></div>
                    <?php endif; ?>
                </div>
                <!-- //// -->
                <div class="g-recaptcha" data-sitekey="6Lde6lIpAAAAAKRucvLvNpae11N-4-Ym5ouD7uz7"></div>
                <?php if (!empty($errors['recaptcha'])): ?>
                <div class="alert alert-warning"><?= $errors['recaptcha']; ?></div>
                <?php endif; ?> 
                <!-- ///// -->
                <div class="form-btn-regi">
                    <input type="submit" class="btn btn-primary" value="Register" name="submit">
                </div>
            </form>
            <div>
            </div>
            <div>
                <p>Already Registered <a href="login.php">Login Here</a></p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
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
