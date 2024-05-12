<?php
session_start();
include_once 'components/connect.php';

// Redirect if user is not logged in as a customer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../home.php");
    exit();
}

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Display user ID
//echo "Welcome, User ID: $user_id";

// Get order ID from GET parameter
$get_id = isset($_GET['get_id']) ? $_GET['get_id'] : '';
if (!$get_id) {
    header('location:orders.php');
    exit();
}

// Function to log user activity
if(isset($_POST['cancel'])){
   $update_orders = $conn->prepare("UPDATE `orders` SET status = ? WHERE order_id = ?");
   $update_orders->bind_param("si", $status, $get_id);
   $status = 'canceled';
   $update_orders->execute();
   header('location:orders.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include './includes/head.php'; ?>
</head>
<body>
<div id="pre-loader">  </div>
<?php include './includes/header.php'; ?>

<div class="all-title-box">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2>Shop</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../home.php">Home</a></li>
                    <li class="breadcrumb-item active">OrderDetails</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<section class="order-details">
    <h1 class="heading">Order Details</h1>
    <div class="box-container">
        <?php
        $grand_total = 0;
        $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE order_id = ? AND user_id = ? LIMIT 1");
        $select_orders->bind_param("ii", $get_id, $user_id);
        $select_orders->execute();
        $result_orders = $select_orders->get_result();

        if ($result_orders->num_rows > 0) {
            while ($fetch_order = $result_orders->fetch_assoc()) {
                $select_product = $conn->prepare("SELECT * FROM `products` WHERE id = ? LIMIT 1");
                $select_product->bind_param("i", $fetch_order['product_id']);
                $select_product->execute();
                $result_product = $select_product->get_result();

                if ($result_product->num_rows > 0) {
                    $fetch_product = $result_product->fetch_assoc();
                    $sub_total = ($fetch_order['price'] * $fetch_order['qty']);
                    $grand_total += $sub_total;
        ?>
                    <div class="box">
                        <div class="col">
                            <p class="title"><i class="fas fa-calendar"></i><?= $fetch_order['date']; ?></p>
                            <img src="uploaded_files/<?= $fetch_product['image']; ?>" class="image" alt="">
                            <p class="price"><i class="fas fa-indian-rupee-sign"></i> <?= $fetch_order['price']; ?> x <?= $fetch_order['qty']; ?></p>
                            <h3 class="name"><?= $fetch_product['name']; ?></h3>
                            <p class="grand-total">Grand Total: <span><i class="fas fa-indian-rupee-sign"></i> <?= $grand_total; ?></span></p>
                        </div>
                        <div class="col">
                            <p class="title">Billing Address</p>
                            <p class="user"><i class="fas fa-user"></i><?= $fetch_order['name']; ?></p>
                            <p class="user"><i class="fas fa-phone"></i><?= $fetch_order['number']; ?></p>
                            <p class="user"><i class="fas fa-envelope"></i><?= $fetch_order['email']; ?></p>
                            <p class="user"><i class="fas fa-map-marker-alt"></i><?= $fetch_order['address']; ?></p>
                            <p class="title">Status</p>
                            <p class="status" style="color:<?php if ($fetch_order['status'] == 'delivered') {
                                echo 'green';
                            } elseif ($fetch_order['status'] == 'canceled') {
                                echo 'red';
                            } else {
                                echo 'orange';
                            }; ?>"><?= $fetch_order['status']; ?></p>
                            <?php if ($fetch_order['status'] == 'canceled') { ?>
                                <a href="checkout.php?get_id=<?= $fetch_product['id']; ?>" class="btn">Order Again</a>
                            <?php } else { ?>
                                <form action="" method="POST">
                                    <input type="submit" value="Cancel Order" name="cancel" class="delete-btn" onclick="return confirm('Cancel this order?');">
                                </form>
                            <?php } ?>
                        </div>
                    </div>
        <?php
                } else {
                    echo '<p class="empty">Product not found!</p>';
                }
            }
        } else {
            echo '<p class="empty">No orders found!</p>';
        }
        ?>
    </div>
</section>

<!-- Include Instagram feed and other HTML elements here -->

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





<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<script src="js/script.js"></script>

<?php include 'components/alert.php'; ?>

<?php
	include './includes/footer.php';
	?>

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
