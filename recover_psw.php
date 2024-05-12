
<?php 
    session_start();
?>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
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

    <link rel="icon" href="Favicon.png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">

    <title>Login Form</title>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light navbar-laravel">
    <div class="container">
        <a class="navbar-brand" href="#">User Password Recover</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>

<main class="login-form" style="background-image: url('img/bbg.jpg'); background-size: cover; height: 100vh; display: flex; align-items: center; justify-content: center;">
   
     <div class="cotainer" style="max-width: 400px; width: 100%;">
        <div class="row justify-content-center">
            <div class="col-md-16">
                <div class="card" style="width: 100%; height: 100%; max-width: 500px;">
                    <div class="card-header">Password Recover</div>
                    <div class="card-body">
                        <form action="#" method="POST" name="recover_psw">
                            <div class="form-group row">
                                <label for="email_address" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>
                                <div class="col-md-8">
                                    <input type="text" id="email_address" class="form-control" name="email" required autofocus>
                                </div>
                            </div>

                            <div class="col-md-6 offset-md-3">
                                <center><input type="submit" value="Recover" name="recover"></center>
                            </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

</main>
</body>
</html>
<?php 
    if(isset($_POST["recover"])){
        include('connect/connection.php');
        $email = $_POST["email"];

        $sql = mysqli_query($connect, "SELECT * FROM login WHERE email='$email'");
        $query = mysqli_num_rows($sql);
  	    $fetch = mysqli_fetch_assoc($sql);

        if(mysqli_num_rows($sql) <= 0){
            ?>
            <script>
                alert("Sorry, no email exists.");
            </script>
            <?php
        } else if($fetch["status"] == 0){
            ?>
            <script>
                alert("Sorry, your account must be verified before you can recover your password!");
                window.location.replace("login.php");
            </script>
            <?php
        } else {
            // Generate a token
            $token = bin2hex(random_bytes(50));

            // Store token and email in session
            $_SESSION['token'] = $token;
            $_SESSION['email'] = $email;

            // Send password reset email
            require "Mail/phpmailer/PHPMailerAutoload.php";
            $mail = new PHPMailer;

            $mail->isSMTP();
            $mail->Host='smtp.gmail.com';
            $mail->Port=587;
            $mail->SMTPAuth=true;
            $mail->SMTPSecure='tls';

            // Sender's email credentials
            $mail->Username='jimjamjim03@gmail.com';
            $mail->Password='cwgorepxglfdtvmv';

            // Set sender and recipient
            $mail->setFrom('jimjamjim03@gmail.com', 'Password Reset'); // Replace with your Gmail email
            $mail->addAddress($_POST["email"]);

            // Email body
            $mail->isHTML(true);
            $mail->Subject="Recover your password";
            $mail->Body="<b>Dear User</b>
            <h3>We received a request to reset your password.</h3>
            <p>Kindly click the below link to reset your password</p>
            http://localhost/login-System/Login-System-main/reset_psw.php
            <br><br>
            <p>With regards,</p>
            <b>Shrungar</b>";

            if(!$mail->send()){
                ?>
                <script>
                    alert("Failed to send email. Please try again.");
                </script>
                <?php
            } else {
                ?>
                <script>
                    window.location.replace("notification.html");
                </script>
                <?php
            }
        }
    }
?>