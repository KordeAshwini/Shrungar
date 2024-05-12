<?php
session_start();
include_once 'components/connect.php';

if(isset($_SESSION['user_id'])) {
    // Get the user ID from the session
    $user_id = $_SESSION['user_id'];

    // Now you can use $user_id as needed, for example:
    //echo "Welcome, User ID: $user_id";
} 
// Check if add to cart form is submitted
if(isset($_POST['add_to_cart'])){
   // Sanitize and validate input data
   $product_id = filter_var($_POST['product_id'], FILTER_SANITIZE_STRING);
   $qty = filter_var($_POST['qty'], FILTER_SANITIZE_STRING);

   // Check if product is already in cart
   $verify_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND product_id = ?");
   if (!$verify_cart) {
       die('Error in prepare statement: ' . $conn->error);
   }
   
   $verify_cart->bind_param("ss", $user_id, $product_id);
   $verify_cart->execute();
   $verify_cart->store_result();
   
   if($verify_cart->num_rows > 0){
      $warning_msg[] = 'Already added to cart!';
   } else {
      // Fetch product price
      $select_price = $conn->prepare("SELECT * FROM `products` WHERE id = ? LIMIT 1");
      $select_price->bind_param("s", $product_id);
      $select_price->execute();
      $fetch_price = $select_price->get_result()->fetch_assoc();
      
      // Insert product into cart
      $insert_cart = $conn->prepare("INSERT INTO `cart` (user_id, product_id, price, qty) VALUES (?, ?, ?, ?)");
      if (!$insert_cart) {
          die('Error in prepare statement: ' . $conn->error);
      }
      
      $insert_cart->bind_param("ssss", $user_id, $product_id, $fetch_price['price'], $qty);
      $insert_cart->execute();
        if ($insert_cart->error) {
            die('Error in insertion query: ' . $insert_cart->error);
        }
         $success_msg[] = 'Added to cart!';
         //$success_msg[] = 'Added to cart!';
   }
}

// Fetch all products
$select_products = $conn->prepare("SELECT * FROM `products`");
$select_products->execute();
$product_data = $select_products->get_result()->fetch_all(MYSQLI_ASSOC);
$json_product_data = json_encode($product_data);
?>



<!DOCTYPE html>
<html lang="en">
<!--<head>
   <!--<meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>View Products</title>-->

   <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">-->

   <!--<link rel="stylesheet" href="css/style.css">--

</head>-->
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
                        <li class="breadcrumb-item"><a href="../home.php">Home</a></li>
                        <li class="breadcrumb-item active">Products</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

<div class="searchBar">
    <input placeholder="Search by Name..." id="searchBar" name="searchBar" type="text" style="padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
    <i class="fa fa-search" style="padding: 10px; background-color: #f2f2f2; border-radius: 5px;"></i>
    
    <!--<i class="fa-solid fa-magnifying-glass glass" id="btn"></i>-->
</div>
<h1 class="heading">All products</h1>

<section class="products" id = "root">


   <!--<div class="box-container">-->
   <!--<form action="" method="POST" class="box">
      <img src="uploaded_files/<//?= $fetch_prodcut['image']; ?>" class="image" alt="">
      <h3 class="name"><//?= $fetch_prodcut['name'] ?></h3>
      <input type="hidden" name="product_id" value="</?//= $fetch_prodcut['id']; ?>">
      <div class="flex">
         <p class="price"><i class="fas fa-indian-rupee-sign"></i><//?= $fetch_prodcut['price'] ?></p>
         <input type="number" name="qty" required min="1" value="1" max="99" maxlength="2" class="qty">
      </div>
      <input type="submit" name="add_to_cart" value="Add to cart" class="btn">
      <a href="checkout.php?get_id=<//?= $fetch_prodcut['id']; ?>" class="delete-btn">Buy now</a>
   </form>-->
               <script>
                const products = <?php echo $json_product_data;?>;

                const displayItem = (items) => {
                    const itemsHtml = items.map((item) => {
                        var { image, name, price } = item;

                        return (
                            `<div class="col-md-3">`+
                            `<div class="box-container">`+
                            `<form action="" method="POST" class="box">` +
                            `<img src="uploaded_files/${image}" class="image img-fluid md-4 " alt="">` +
                            `<h3 class="name">${name}</h3>` +
                            `<input type="hidden" name="product_id" value="${item.id}">` +
                            `<div class="d-flex justify-content-between align-items-end">` +
                            `<p class="price"><i class="fas fa-indian-rupee-sign"></i>${price}</p>` +
                            `<input type="number" name="qty" required min="1" value="1" max="99" maxlength="2" class="qty">` +
                            `</div>` +
                            `<div class="text-center">` +
                            `<input type="submit" name="add_to_cart" value="Add to Cart" class="btn">` +
                            `<a href="checkout.php?get_id=${item.id}" class="delete-btn btn btn-danger">Buy Now</a>` +
                            `</div>` +
                            `</form>` +
                            `</div>`+
                            `</div>`
                            
                        );
                    });

                    const rows = [];
                    for (let i = 0; i < itemsHtml.length; i += 4) {
                        rows.push(`<div class="row">${itemsHtml.slice(i, i + 4).join('')}</div>`);
                    }

                    document.getElementById('root').innerHTML = rows.join('');
                };

                document.getElementById('searchBar').addEventListener('keyup', (e) => {
                    const searchData = e.target.value.toLowerCase();
                    const filteredData = products.filter((item) => {
                        return item.name.toLowerCase().includes(searchData);
                    });
                    displayItem(filteredData);
                });

                displayItem(products);
            </script>
            
            
            
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

        buyNowButtons.forEach(function(button) {
            button.addEventListener("click", function(event) {
                // Check if user is not logged in
                if (!isLoggedIn()) {
                    event.preventDefault(); // Prevent default action
                    alert("Please log in to proceed to checkout.");
                    redirectToLogin();
                }
                // Check if user role is not customer
                else if (getUserRole() !== 'customer') {
                    event.preventDefault(); // Prevent default action
                    alert("Only customers can proceed to checkout.");
                }
            });
        });
    }

    // Check if user is logged in
    function isLoggedIn() {
        return <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    }

    // Get user role
    function getUserRole() {
        return '<?php echo isset($_SESSION['role']) ? $_SESSION['role'] : ''; ?>';
    }

    // Redirect to login page
    function redirectToLogin() {
        window.location.href = "../login.php"; // Redirect to login page
    }

    // Call the function to disable buttons based on user role
    disableButtonsByRole();
});
</script>


   <!--</div>-->
 
   

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
