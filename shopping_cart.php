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

// Update cart item quantity
if (isset($_POST['update_cart'])) {
    $cart_id = $_POST['cart_id'];
    $qty = $_POST['qty'];

    // Prepare update query
    $update_qty = "UPDATE `cart` SET qty = ? WHERE cart_id = ? AND user_id = ?";
    $stmt = $conn->prepare($update_qty);

    // Check if prepare() succeeded
    if ($stmt === false) {
        die('Error preparing update statement: ' . $conn->error);
    }

    // Bind parameters and execute query
    $stmt->bind_param('iii', $qty, $cart_id, $user_id);
    if ($stmt->execute()) {
        $success_msg[] = 'Cart quantity updated!';
    } else {
        $error_msg[] = 'Error updating cart quantity: ' . $stmt->error;
    }
}

// Delete cart item
if (isset($_POST['delete_item'])) {
    $cart_id = $_POST['cart_id'];

    // Prepare delete query
    $delete_cart_id = "DELETE FROM `cart` WHERE cart_id = ? AND user_id = ?";
    $stmt = $conn->prepare($delete_cart_id);

    // Check if prepare() succeeded
    if ($stmt === false) {
        die('Error preparing delete statement: ' . $conn->error);
    }

    // Bind parameters and execute query
    $stmt->bind_param('ii', $cart_id, $user_id);
    if ($stmt->execute()) {
        $success_msg[] = 'Cart item deleted!';
    } else {
        $error_msg[] = 'Error deleting cart item: ' ;
    }
}

// Empty cart
if (isset($_POST['empty_cart'])) {
    // Prepare empty cart query
    $delete_cart_id = "DELETE FROM `cart` WHERE user_id = ?";
    $stmt = $conn->prepare($delete_cart_id);

    // Check if prepare() succeeded
    if ($stmt === false) {
        die('Error preparing empty cart statement: ' . $conn->error);
    }

    // Bind parameters and execute query
    $stmt->bind_param('i', $user_id);
    if ($stmt->execute()) {
        $success_msg[] = 'Cart emptied!';
    } else {
        $error_msg[] = 'Error emptying cart: ' . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include './includes/head.php'; ?>
<body>
<div id="pre-loader">  </div>
<?php include './includes/header.php'; ?>

<div class="all-title-box">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2>Shop</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../project/view_products.php">Shop</a></li>
                    <li class="breadcrumb-item active">Cart</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<section class="products">
    <h1 class="heading">Shopping Cart</h1>
    <div class="box-container row">
        <?php
        $grand_total = 0;
        $select_cart = "SELECT * FROM `cart` WHERE user_id = ?";
        $stmt = $conn->prepare($select_cart);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($fetch_cart = $result->fetch_assoc()) {
                $product_id = $fetch_cart['product_id'];

                // Fetch product details from the products table
                $select_product = "SELECT * FROM `products` WHERE id = ?";
                $stmt_product = $conn->prepare($select_product);
                $stmt_product->bind_param('i', $product_id);
                $stmt_product->execute();
                $result_product = $stmt_product->get_result();
                $fetch_product = $result_product->fetch_assoc();

                ?>
                <div class="col-md-3">
                    <form action="" method="POST" class="box">
                        <input type="hidden" name="cart_id" value="<?= $fetch_cart['cart_id']; ?>">
                        <img src="uploaded_files/<?= $fetch_product['image']; ?>" class="image" alt="">
                        <h3 class="name"><?= $fetch_product['name']; ?></h3>
                        <div class="flex">
                            <p class="price"><i class="fas fa-indian-rupee-sign"></i> <?= $fetch_product['price']; ?></p>
                            <input type="number" name="qty" required min="1" value="<?= $fetch_cart['qty']; ?>" max="99" maxlength="2" class="qty">
                            <button type="submit" name="update_cart" class="fas fa-edit"></button>
                        </div>
                        <p class="sub-total">Subtotal: <span><i class="fas fa-indian-rupee-sign"></i> <?= $sub_total = ($fetch_cart['qty'] * $fetch_product['price']); ?></span></p>
                        <input type="submit" value="Delete" name="delete_item" class="delete-btn" onclick="return confirm('Delete this item?');">
                    </form>
                </div>
                <?php
                $grand_total += $sub_total;
            }
        } else {
            echo '<p class="empty">Your cart is empty!</p>';
        }
        ?>
    </div>
    <?php if ($grand_total != 0) { ?>
        <div class="cart-total">
            <p>Grand Total: <span><i class="fas fa-indian-rupee-sign"></i> <?= $grand_total; ?></span></p>
            <form action="" method="POST">
                <input type="submit" value="Empty Cart" name="empty_cart" class="delete-btn" onclick="return confirm('Empty your cart?');">
            </form>
            <a href="checkout.php" class="btn">Proceed to Checkout</a>
        </div>
    <?php } ?>
</section>
<!--tagram Feed  -->
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

    <script>
document.addEventListener("DOMContentLoaded", function() {
    const addToCartButtons = document.querySelectorAll('[name="add_to_cart"]');
    const buyNowButtons = document.querySelectorAll('.delete-btn');

    // Function to disable buttons based on user role
    function disableButtonsByRole() {
        addToCartButtons.forEach(function(button) {
            button.addEventListener("click", function(event) {
                // Check if user is not logged in
                if (!isLoggedIn()) {
                    event.preventDefault(); // Prevent default action
                    alert("Please log in to add items to the cart.");
                    redirectToLogin();
                }
                // Check if user role is not customer
                else if (getUserRole() !== 'customer') {
                    event.preventDefault(); // Prevent default action
                    alert("Only customers can add items to the cart.");
                }
            });
        });
    }
}
</script>
    <!-- End Instagram Feed  -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<script src="js/script.js"></script>

<?php include 'components/alert.php'; ?>
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

<!-- Include your footer, Instagram feed, and JavaScript includes here -->

</body>
</html>
