<?php
session_start();
include('connect/connection.php');

// Check if the user is already logged in, if yes, redirect to home page
/*if(isset($_SESSION['user_id'])) {
    // Assuming 'user_id' is the session variable to check if the user is logged in
    if($_SESSION['role'] == 'customer') {
        header('location: home.php');
    } elseif($_SESSION['role'] == 'business') {
        header('location: users-profile.php');
    } elseif($_SESSION['role'] == 'admin') {
        header('location: admin-dash.php');
    }
    exit();
}*/

if(isset($_POST["login"])){
    $email = mysqli_real_escape_string($connect, trim($_POST['email']));
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    $sql = mysqli_query($connect, "SELECT * FROM login WHERE email = '$email' AND role = '$role'");
    $count = mysqli_num_rows($sql);

    if($count > 0){
        $fetch = mysqli_fetch_assoc($sql);
        $hashpassword = $fetch["password"];

        if($fetch["status"] == 0){
            $_SESSION['error'] = "Please verify your email account before login.";
        } else if(password_verify($password, $hashpassword)){
            if ($fetch["status"] == 1) { // Assuming status 1 indicates successful login
                // Set session variables
                $_SESSION['user_id'] = $fetch['userId']; // Assuming 'userId' is the column name storing user id
                $_SESSION['email'] = $fetch['email'];
                $_SESSION['role'] = $fetch['role'];
            
                // Redirect based on role
                if ($role == 'customer') {
                    header('location: home.php');
                    exit();
                } elseif ($role == 'business') {
                    header('location: users-profile.php');
                    exit();
                } elseif ($role == 'admin') {
                    header('location: project/admindash.php');
                    exit();
                }
            }
        } else {
            $_SESSION['error'] = "Email or password  invalid, please try again.";
			?>
			<script>
                alert("Email or password or role invalid, please try again.");
            </script>
			<?php
        }
    } else {
        $_SESSION['error'] = "Email or password invalid,   please try again.";
		?>
		<script>
                alert("Email or password or role invalid, please try again.");
            </script>
		<?php	
    }
}
?>


<!-- Your HTML code for the login page -->
<!-- Add a section to display errors, if any -->


<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!-- Your HTML code for the login page -->
<html>
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="styles.css">

    <link rel="icon" href="Favicon.png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />

    <title>Login Form</title>
</head>
<body>
<div id="pre-loader">  </div>
<nav class="navbar navbar-expand-lg navbar-light navbar-laravel">
    <div class="container">
        <a class="navbar-brand" href="#">Login Form</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php" style="font-weight:bold; color:black; text-decoration:underline">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="register.php">Register</a>
                </li>
            </ul>

        </div>
    </div>
</nav>

<main class="login-form" style="background-image: url('img/bbg.jpg'); background-size: cover; height: 100vh; display: flex; align-items: center; justify-content: center;">
   
     <div class="cotainer" style="max-width: 400px; width: 100%;">
        <div class="row justify-content-center">
            <div class="col-md-16">
                <div class="card" style="width: 100%; height: 100%; max-width: 500px;">
                    <div class="card-header">Login</div>
                    <div class="card-body">
                        <form action="#" method="POST" name="login">
                            <div class="form-group row">
                                <label for="email_address" class="col-md-4 col-form-label text-md-right">E-Mail</label>
                                <div class="col-md-8">
                                    <input type="text" id="email_address" class="form-control" name="email" required autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>
                                <div class="col-md-8">
                                    <input type="password" id="password" class="form-control" name="password" required>
                                    <i class="bi bi-eye-slash" id="togglePassword"></i>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="role" class="col-md-4 col-form-label text-md-right">Role</label>
                                <div class="col-md-8">
                                    <select id="role" class="form-control" name="role" required>
                                        <option value="customer">Customer Account</option>
                                        <option value="business">Business Account</option>
										 <option value="admin">Admin</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6 offset-md-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="remember"> Remember Me
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-8 offset-md-2">
                                <center><input type="submit" value="Login" name="login"></center>
                                <a href="recover_psw.php" class="btn btn-link">
                                    Forgot Your Password?
                                </a>
                            </div>
                    </div>
                    </form>
					<a href="home.php" class="btn btn-link">
                                    Go to Home
                                </a>
                </div>
				
            </div>
        </div>
    </div>
    </div>
    

</main>
<script>
        var loader=document.getElementById('pre-loader');
        window.addEventListener('load',function(){
             setTimeout(function(){
                loader.style.display='none';
            },
             1500);
        })
        </script>
</body>
<?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-danger" role="alert">
        <?php echo $_SESSION['error']; ?>
    </div>
    <?php unset($_SESSION['error']); // Remove the error after displaying ?>
<?php endif; ?>
</html>
<script>
    const toggle = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    toggle.addEventListener('click', function(){
        if(password.type === "password"){
            password.type = 'text';
        }else{
            password.type = 'password';
        }
        this.classList.toggle('bi-eye');
    });
</script>
