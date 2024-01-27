<!DOCTYPE html>
<html>
<head>
	<title>Change Password</title>
	<link rel="stylesheet" type="text/css" href="Change.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
</head>
<body>
    <form action="change-p.php" method="post">
     	<h2>Change Password</h2>
     	<?php if (isset($_GET['error'])) { ?>
     		<p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
     	<?php } ?>

     	<?php if (isset($_GET['success'])) { ?>
            <p class="success"><?php echo htmlspecialchars($_GET['success']); ?></p>
        <?php } ?>
		
		<div class="form-group position-relative">
     		<label>Old Password</label>
			<div class="input-group">
     			<input type="password" name="op" placeholder="Old Password" class="form-control" value="<?php echo isset($_SESSION['old_password']) ? $_SESSION['old_password'] : ''; ?>">
				<button type="button" class="btn toggle-password position-absolute end-0 top-0">
        			<i class="bi bi-eye-slash"></i>
        		</button>
			</div>
		</div>	
		<br>
		
		<div class="form-group position-relative">
     		<label>New Password</label>
			<div class="input-group">
     			<input type="password" name="np" placeholder="New Password"  class="form-control" value="<?php echo isset($_SESSION['new_password']) ? $_SESSION['new_password'] : ''; ?>">
				<button type="button" class="btn toggle-password position-absolute end-0 top-0">
       				<i class="bi bi-eye-slash"></i>
        		</button>
			</div>
		</div>
		<br>
		
		<div class="form-group position-relative">
     		<label>Confirm New Password</label>
			<div class="input-group">
     			<input type="password" name="c_np" placeholder="Confirm New Password" class="form-control" value="<?php echo isset($_SESSION['confirm_new_password']) ? $_SESSION['confirm_new_password'] : ''; ?>">
				<button type="button" class="btn toggle-password position-absolute end-0 top-0">
        			<i class="bi bi-eye-slash"></i>
        		</button>
			</div>
		</div>
		<br>
			
	<div>
        <button type="submit" name="reset">Reset</button>
        <?php
        if (isset($_POST['reset'])) {
        }
        ?>
    </div>	
          <div><a href="home.php" class="ca">Home</a></div>
    </form>
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
