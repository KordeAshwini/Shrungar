<?php
session_start();
require_once 'components/connect.php'; // Include the database connection file

// Redirect to login page if the user is not a customer
if($_SESSION['role'] != 'customer'){
    header("Location: ../login.php");
}

// Redirect to login page if the user is not a customer
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'customer'){
    header("Location: ../login.php");
    exit(); // Stop further script execution after redirection
}
$user_id = $_SESSION['user_id']; // Retrieve user ID from session
//echo "Welcome, User ID: $user_id";


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <!-- Include head content -->
   <?php include './includes/head.php'; ?>
</head>
<body>
<div id="pre-loader">  </div>
   <!-- Include header -->
   <?php include './includes/header.php'; ?>

   <!-- Breadcrumb -->
   <div class="all-title-box">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2>Shop</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../home.php">Home</a></li>
                        <li class="breadcrumb-item active">Orders</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <section class="orders">
   <h1 class="heading">My Orders</h1>
   <div class="box-container">
      <?php
         // Fetch orders for the current user
         $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ? ORDER BY date DESC");
         $select_orders->bind_param("i", $user_id); // Bind parameter
         $select_orders->execute();
         $result_orders = $select_orders->get_result(); // Get result set
         
         // Check if any orders exist
         if($result_orders->num_rows > 0){
            // Loop through each order
            while($fetch_order = $result_orders->fetch_assoc()){
               // Fetch product details for the order
               $select_product = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
               $select_product->bind_param("i", $fetch_order['product_id']); // Bind parameter
               $select_product->execute();
               $result_product = $select_product->get_result(); // Get result set
               
               // Check if product exists
               if($result_product->num_rows > 0){
                  // Loop through each product
                  while($fetch_product = $result_product->fetch_assoc()){
      ?>
      <!-- Order Box -->
      <div class="box" <?php if($fetch_order['status'] == 'canceled'){echo 'style="border:.2rem solid red";';}; ?>>
         <a href="view_order.php?get_id=<?= $fetch_order['order_id']; ?>">
            <p class="date"><i class="fa fa-calendar"></i><span><?= $fetch_order['date']; ?></span></p>
            <img src="uploaded_files/<?= $fetch_product['image']; ?>" class="image" alt="">
            <h3 class="name"><?= $fetch_product['name']; ?></h3>
            <p class="price"><i class="fas fa-indian-rupee-sign"></i> <?= $fetch_order['price']; ?> x <?= $fetch_order['qty']; ?></p>
            <p class="status" style="color:<?php if($fetch_order['status'] == 'delivered'){echo 'green';}elseif($fetch_order['status'] == 'canceled'){echo 'red';}else{echo 'orange';}; ?>"><?= $fetch_order['status']; ?></p>
         </a>
      </div>
      <?php
                  }
               }
            }
         } else {
            // Display a message if no orders found
            echo '<p class="empty">No orders found!</p>';
         }
      ?>
   </div>
</section>


<!-- Start Instagram Feed  -->
     <div class="instagram-box">
        <div class="main-instagram owl-carousel owl-theme">
            <div class="item">
                <div class="ins-inner-box">
                    <img src="../images/earring25.jpg" alt="" />
                    <div class="hov-in">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="ins-inner-box">
                    <img src="../images/bangles9.jpeg" alt="" />
                    <div class="hov-in">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="ins-inner-box">
                    <img src="../images/bracelate20.jpeg" alt="" />
                    <div class="hov-in">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="ins-inner-box">
                    <img src="../images/earring21.jpg" alt="" />
                    <div class="hov-in">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="ins-inner-box">
                    <img src="../images/floral21.jpeg" alt="" />
                    <div class="hov-in">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="ins-inner-box">
                    <img src="../images/necklace8.jpg" alt="" />
                    <div class="hov-in">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="ins-inner-box">
                    <img src="../images/bangles22.jpeg" alt="" />
                    <div class="hov-in">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="ins-inner-box">
                    <img src="../images/quilling19.jpeg" alt="" />
                    <div class="hov-in">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="ins-inner-box">
                    <img src="../images/mehendi1.jpeg" alt="" />
                    <div class="hov-in">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="ins-inner-box">
                    <img src="../images/hairstyle1.jpeg" alt="" />
                    <div class="hov-in">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="ins-inner-box">
                    <img src="../images/makeup3.jpeg" alt="" />
                    <div class="hov-in">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="ins-inner-box">
                    <img src="../images/nailart2.jpeg" alt="" />
                    <div class="hov-in">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Instagram Feed  -->

<!-- Include footer -->
<?php include './includes/footer.php'; ?>

<!-- Back to top button -->
<a href="#" id="back-to-top" title="Back to top" style="display: none;">&uarr;</a>

<!-- ALL JS FILES -->
<script src="../js/jquery-3.2.1.min.js"></script>
<script src="../js/popper.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<!-- ALL PLUGINS -->
<script src="../js/jquery.superslides.min.js"></script>
<script src="../js/bootstrap-select.js"></script>
<script src="../js/inewsticker.js"></script>
<script src="../js/bootsnav.js."></script>
<script src="../js/images-loded.min.js"></script>
<script src="../js/isotope.min.js"></script>
<script src="../js/owl.carousel.min.js"></script>
<script src="../js/baguetteBox.min.js"></script>
<script src="../js/form-validator.min.js"></script>
<script src="../js/contact-form-script.js"></script>
<script src="../js/custom.js"></script>
<link rel="stylesheet" href="css/style.css">
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
