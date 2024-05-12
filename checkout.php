<?php
session_start();
require_once 'components/connect.php'; // Include the database connection file

// Redirect to login page if the user is not a customer
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    header("Location: ../login.php");
    exit(); // Stop further script execution after redirection
}
$user_id = $_SESSION['user_id']; // Retrieve user ID from session
//echo "Welcome, User ID: $user_id";

// Include head content
include './includes/head.php';

// Initialize warning message array
$warning_msg = [];

if (isset($_POST['place_order'])) {
    // Retrieve and sanitize form data
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
    //$address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
    $address = filter_var($_POST['flat'] . ', ' . $_POST['street'] . ', ' . $_POST['city'] . ', ' . $_POST['country'] . ' pincode - ' . $_POST['pin_code'], FILTER_SANITIZE_STRING);
    $address_type = filter_var($_POST['address_type'], FILTER_SANITIZE_STRING);
    $method = filter_var($_POST['method'], FILTER_SANITIZE_STRING);

    // Check if there are products in the cart
    $verify_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
    $verify_cart->bind_param("i", $user_id);
    $verify_cart->execute();
    $verify_cart_result = $verify_cart->get_result();

    if ($verify_cart_result->num_rows > 0) {
        // If there are products in the cart, insert orders for each product
        while ($f_cart = $verify_cart_result->fetch_assoc()) {
            $insert_order = $conn->prepare("INSERT INTO `orders` (user_id, cart_id, name, number, email, address, address_type, method, product_id, price, qty) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insert_order->bind_param("iisssssssss", $user_id, $f_cart['cart_id'], $name, $number, $email, $address, $address_type, $method, $f_cart['product_id'], $f_cart['price'], $f_cart['qty']);
            $insert_order->execute();
        }

        // After inserting orders, delete items from cart
        $delete_cart_id = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
        $delete_cart_id->bind_param("i", $user_id);
        $delete_cart_id->execute();
        header('location:orders.php');
    } else {
        $warning_msg[] = 'Your cart is empty!';
    }
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
                        <li class="breadcrumb-item active">Checkout</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <section class="checkout" style="padding: 20px; background-color: #f9f9f9;">
    <h4 class="heading" style="margin-bottom: 20px;">Checkout Summary</h4>
    <div class="row">
        <div class="col-md-7">
            <form action="" method="POST" style="background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                <h3 style="margin-bottom: 20px;">Billing Details</h3>
                <div class="flex" style="display: flex; flex-wrap: wrap;">
                    <div class="" style="flex: 1; margin-right: 20px;">
                        <p style="margin-bottom: 5px;">Your Name <span>*</span></p>
                        <input type="text" name="name" required maxlength="50" placeholder="Enter your name" class="a" style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc; margin-bottom: 10px;">
						 <p>your number <span>*</span></p>
               <input type="number" name="number" required maxlength="10" placeholder="enter your number" class="a" min="0" max="9999999999"style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc; margin-bottom: 10px;">
			   <p>your email <span>*</span></p>
               <input type="email" name="email" required maxlength="50" placeholder="enter your email" class="a" style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc; margin-bottom: 10px;">
               <p>payment method <span>*</span></p>
               <select name="method" class="a" style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc; margin-bottom: 10px;" required>
                  <option value="cash on delivery">cash on delivery</option>
                  <option value="credit or debit card">credit or debit card</option>
                  <option value="net banking">net banking</option>
                  <option value="UPI or wallets">UPI or RuPay</option>
               </select>
               <p>address type <span>*</span></p>
               <select name="address_type" class="a" required style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc; margin-bottom: 10px;"> 
                  <option value="home">home</option>
                  <option value="office">office</option>
               </select>
                        <!-- Repeat similar styles for other input fields -->
                    </div>
                    <div class="box" style="flex: 1;">
                        <p style="margin-bottom: 5px;">Address Line 01 <span>*</span></p>
                        <input type="text" name="flat" required maxlength="50" placeholder="e.g. Flat & building number" class="a" style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc; margin-bottom: 10px;">
						 <p>address line 02 <span>*</span></p>
               <input type="text" name="street" required maxlength="50" placeholder="e.g. street name & locality" class="a" style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc; margin-bottom: 10px;">
               <p>city name <span>*</span></p>
               <input type="text" name="city" required maxlength="50" placeholder="enter your city name" class="a" style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc; margin-bottom: 10px;">
               <p>country name <span>*</span></p>
               <input type="text" name="country" required maxlength="50" placeholder="enter your country name" class="a" style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc; margin-bottom: 10px;">
               <p>pin code <span>*</span></p>
               <input type="number" name="pin_code" required maxlength="6" placeholder="e.g. 123456" class="a" min="0" max="999999" style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc; margin-bottom: 10px;">
                        <!-- Repeat similar styles for other input fields -->
                    </div>
                </div>
                <input type="submit" value="Place Order" name="place_order" class="b" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; margin-top: 20px; display: block; margin: 0 auto;">
            </form>

            </div>

            <div class="summary">
    <h3 class="title">Cart items</h3>
    <?php
    $grand_total = 0;
    if(isset($_GET['get_id'])){
        $select_get = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
        if($select_get) {
            $select_get->bind_param("i", $_GET['get_id']);
            if($select_get->execute()) {
                $result = $select_get->get_result();
                if($result->num_rows > 0) {
                    while($fetch_get = $result->fetch_assoc()) {
                        // Display product details
                        ?>

         <div class="flex">
            <img src="uploaded_files/<?= $fetch_get['image']; ?>" class="image" alt="">
            <div>
               <h3 class="name"><?= $fetch_get['name']; ?></h3>
               <p class="price"><i class="fas fa-indian-rupee-sign"></i> <?= $fetch_get['price']; ?> x 1</p>
            </div>
         </div>
         <?php
           $grand_total += $fetch_get['price'];
        }
    } else {
        echo '<p class="empty">No product found with ID: ' . $_GET['get_id'] . '</p>';
    }
} else {
    echo "Error executing the SQL query: " . $select_get->error;
}
} else {
echo "Error preparing SQL statement: " . $conn->error;
}
} else{
                $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
                $select_cart->bind_param("i", $user_id);
                $select_cart->execute();
                $result = $select_cart->get_result(); // Get the result set
                
                if($result->num_rows > 0) {
                    while($fetch_cart = $result->fetch_assoc()) {
                        $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
                        $select_products->bind_param("i", $fetch_cart['product_id']); // Bind the parameter
                        $select_products->execute(); // Execute the prepared statement
                        $fetch_product = $select_products->get_result()->fetch_assoc(); // Fetch the result
                        
                        $sub_total = ($fetch_cart['qty'] * $fetch_product['price']);
                        $grand_total += $sub_total;
                        
            
         ?>
         <div class="flex">
            <img src="uploaded_files/<?= $fetch_product['image']; ?>" class="image" alt="">
            <div>
               <h3 class="name"><?= $fetch_product['name']; ?></h3>
               <p class="price"><i class="fas fa-indian-rupee-sign"></i> <?= $fetch_product['price']; ?> x <?= $fetch_cart['qty']; ?></p>
            </div>
         </div>
         <?php
                  }
               }else{
                  echo '<p class="empty">your cart is empty</p>';
               }
            }
         ?>
         <div class="grand-total"><span>grand total :</span><p><i class="fas fa-indian-rupee-sign"></i> <?= $grand_total; ?></p></div>
      </div>
        </div>
    </section>

      <!-- Start Instagram Feed  -->
   <div class="instagram-box">
        <div class="main-instagram owl-carousel owl-theme">
            <div class="item">
                <div class="ins-inner-box">
                    <img src="images/earring25.jpg" alt="" />
                    <div class="hov-in">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="ins-inner-box">
                    <img src="images/bangles9.jpeg" alt="" />
                    <div class="hov-in">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="ins-inner-box">
                    <img src="images/bracelate20.jpeg" alt="" />
                    <div class="hov-in">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="ins-inner-box">
                    <img src="images/earring21.jpg" alt="" />
                    <div class="hov-in">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="ins-inner-box">
                    <img src="images/floral21.jpeg" alt="" />
                    <div class="hov-in">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="ins-inner-box">
                    <img src="images/necklace8.jpg" alt="" />
                    <div class="hov-in">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="ins-inner-box">
                    <img src="images/bangles22.jpeg" alt="" />
                    <div class="hov-in">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="ins-inner-box">
                    <img src="images/quilling19.jpeg" alt="" />
                    <div class="hov-in">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="ins-inner-box">
                    <img src="images/mehendi1.jpeg" alt="" />
                    <div class="hov-in">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="ins-inner-box">
                    <img src="images/hairstyle1.jpeg" alt="" />
                    <div class="hov-in">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="ins-inner-box">
                    <img src="images/makeup3.jpeg" alt="" />
                    <div class="hov-in">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="ins-inner-box">
                    <img src="images/nailart2.jpeg" alt="" />
                    <div class="hov-in">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Instagram Feed  -->

    <!-- footer php include -->
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
