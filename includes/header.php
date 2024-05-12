<?php

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    //$user_id = $_SESSION['user_id'];
    
    //include_once 'components/connect.php';
    // Check if user is logged in
    if(isset($_SESSION['email'])) {
        $username = $_SESSION['email'];
        $logoutLink = 'logout.php';
        $logoutText = 'Logout';
    } else {
        $username = 'My Account';
        $logoutLink = 'login.php';
        $logoutText = 'Login';
    }
?>
<!-- Start Main Top -->
<div class="main-top">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="right-phone-box">
                    <p>Call US :- <a href="tel:+11 900 800 100"> +11 900 800 100</a></p>
                </div>
                <div class="our-link">
                    <ul>
                        <li><a href="#"><i class="fa fa-user s_color"></i> <?php echo $username; ?></a></li>
                        <li><a href="#"><i class="fas fa-location-arrow"></i> Our location</a></li>
                        <li><a href="contact-us.php"><i class="fas fa-headset"></i> Contact Us</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="login-box">
                    <button style="background-color: #b0b435; 
                                    border: none;
                                    color: white;
                                    padding: 1px 25px;
                                    text-align: center;
                                    text-decoration: none;
                                    display: inline-block;
                                    font-size: 19px;
                                    margin: 4px 2px;
                                    cursor: pointer;
                                    border-radius: 0px;
                                    transition: background-color 0.3s;">
                        <a href="<?php echo $logoutLink; ?>" style="text-decoration: none; color: white; display: block; height: 100%; width: 100%;"><?php echo $logoutText; ?></a>
                    </button>
                </div>
                <div class="text-slid-box">
                    <div id="offer-box" class="carouselTicker">
                        <ul class="offer-box">
                            <li><i class="fab fa-opencart"></i> 20% off Entire Purchase Promo code: offT80</li>
                            <li><i class="fab fa-opencart"></i> 50% - 80% off on Earrings</li>
                            <li><i class="fab fa-opencart"></i> Off 10%! Shop Bangles</li>
                            <li><i class="fab fa-opencart"></i> Off 50%! Shop Now</li>
                            <li><i class="fab fa-opencart"></i> Off 10% on services</li>
                            <li><i class="fab fa-opencart"></i> 50% - 80% off on Necklace</li>
                            <li><i class="fab fa-opencart"></i> 20% off Entire Purchase Promo code: offT30</li>
                            <li><i class="fab fa-opencart"></i> Off 50%! Shop Now</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Main Top -->

<!-- Start Main Top -->
<header class="main-header">
    <!-- Start Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light navbar-default bootsnav">
        <div class="container">
            <!-- Start Header Navigation -->
            <div class="navbar-header">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-menu" aria-controls="navbars-rs-food" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="home.php"><img src="img/logo.png" class="logo" alt=""></a>
            </div>
            <!-- End Header Navigation -->

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="navbar-menu">
                <ul class="nav navbar-nav ml-auto" data-in="fadeInDown" data-out="fadeOutUp">
                    <li class="nav-item "><a class="nav-link" href="home.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                    <li class="dropdown">
                        <a href="#" class="nav-link dropdown-toggle arrow" data-toggle="dropdown">SHOP</a>
                        <ul class="dropdown-menu">
                            <li><a href="./project/view_products.php">Products</a></li>
                            <li><a href="./project/view_services.php">Services</a></li>
                            <li><a href="./project/shopping_cart.php">Cart</a></li>
                            <li><a href="./project/checkout.php">Checkout</a></li>
                            <li><a href="./project/orders.php">My Orders</a></li>
                            <li><a href="users-profile.php">Profile</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="contact-us.php">Contact Us</a></li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- Start Side Menu -->
    </nav>
    <!-- End Navigation -->
</header>
<!-- End Main Top -->
