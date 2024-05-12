<?php 
session_start(); 
include('connect/connection.php');

if(isset($_POST["register"])){
    $email = $_POST["email"];
    $password = $_POST["password"];
    $role = $_POST["role"];

    $check_query = mysqli_query($connect, "SELECT * FROM login where email ='$email'");
    $rowCount = mysqli_num_rows($check_query);

    if(!empty($email) && !empty($password) && !empty($role)){
        if($rowCount > 0){
            ?>
            <script>
                alert("User with email already exists!");
            </script>
            <?php
        } else {
            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            $result = mysqli_query($connect, "INSERT INTO login (email, password, status, role) VALUES ('$email', '$password_hash', 0, '$role')");

            if($result){
                // Set session variables
                $_SESSION['email'] = $email; // Set session email to user's email
                $_SESSION['role'] = $role;

                $otp = rand(100000,999999);
                $_SESSION['otp'] = $otp;
                $_SESSION['email'] = $email;

                // Assuming you've configured PHPMailer properly
                require "Mail/phpmailer/PHPMailerAutoload.php";
                $mail = new PHPMailer;

                // Email configuration
                $mail->isSMTP();
                $mail->Host='smtp.gmail.com';
                $mail->Port=587;
                $mail->SMTPAuth=true;
                $mail->SMTPSecure='tls';

                // Gmail credentials
                $mail->Username='jimjamjim03@gmail.com';
                $mail->Password='cwgorepxglfdtvmv';

                // Email content
                $mail->setFrom('jimjamjim03@gmail.com', 'OTP Verification'); // Replace with your Gmail email
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject="Your verify code";
                $mail->Body="<p>Dear user, </p> <h3>Your verify OTP code is $otp <br></h3>
                <br><br>
                <p>With regards,</p>
                <b>From Shrungar</b>";

                if(!$mail->send()){
                    ?>
                    <script>
                        alert("Registration Failed. Invalid Email.");
                    </script>
                    <?php
                } else {
                    // Registration successful, redirect user to verification page
                    ?>
                    <script>
                        alert("Registration Successful. OTP sent to <?php echo $email; ?>");
                        window.location.replace('verification.php');
                    </script>
                    <?php
                }
            }
        }
    }
}
?>






<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />
<!------ Include the above in your HEAD tag ---------->

<!doctype html>
<html lang="en">
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

    <title>Register Form</title>
</head>
<body>
<div id="pre-loader">  </div>
<nav class="navbar navbar-expand-lg navbar-light navbar-laravel">
    <div class="container">
        <a class="navbar-brand" href="#">Register Form</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="login.php" >Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="register.php" style="font-weight:bold; color:black; text-decoration:underline">Register</a>
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
                    <div class="card-header">Register</div>
                    <div class="card-body">
                        <form action="#" method="POST" name="register">
                            <div class="form-group row">
                                <label for="email_address" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>
                                <div class="col-md-8">
                                    <input type="text" id="email_address" class="form-control" name="email" required autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">Password  </label>
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
                           

                            <div class="col-md-6 offset-md-3">
                               <center><input type="submit" value="Register" name="register"></center>
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
