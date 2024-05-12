<?php
session_start();
include_once 'components/connect.php';

// Check if user is logged in
if(isset($_SESSION['user_id'])) {
    // Get the user ID from the session
    $user_id = $_SESSION['user_id'];

    // Now you can use $user_id as needed, for example:
    //echo "Welcome, User ID: $user_id";
} 

// Initialize role variable
$role = '';

// Check if the 'role' key exists in the session array
if(isset($_SESSION['role'])) {
    $role = $_SESSION['role'];
}

// Function to fetch services data
function fetchServicesData($conn) {
    $services_data = [];
    $query = "SELECT * FROM `services`";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $services_data[] = $row;
        }
    }
    return $services_data;
}

$services_data = fetchServicesData($conn);
$json_services_data = json_encode($services_data);
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
                <h2>Services</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../home.php">Home</a></li>
                    <li class="breadcrumb-item active">Services</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="searchBar">
    <input placeholder="Search by Name..." id="searchBar" name="searchBar" type="text" style="padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
    <i class="fa fa-search" style="padding: 10px; background-color: #f2f2f2; border-radius: 5px;"></i>
	
    <input placeholder="Search by Location..." id="locationSearch" name="locationSearch" type="text" style="padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
    <!--<i class="fa fa-search" style="padding: 10px; background-color: #f2f2f2; border-radius: 5px;"></i>-->
    <button id="searchByLocationBtn" style="padding: 8px 16px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer;">Search</button>
</div>


<h1 class="heading">All Services</h1>

<section class="products" id="root">

<script>
const services = <?php echo $json_services_data;?>;

const displayService = (services) => {
    const serviceHtml = services.map((service) => {
        var { image, name, price, location } = service;

        return (
            `<div class="col-md-3">`+
            `<div class="box-container">`+
            `<form action="" method="POST" class="box">` +
            `<img src="uploaded_files/${image}" class="image img-fluid md-4 " alt="">` +
            `<h3 class="name">${name}</h3>` +
            `<p class="location"><strong>Location:</strong> ${location}</p>` + // Display location
            `<input type="hidden" name="product_id" value="${service.id}">` +
            `<div class="d-flex justify-content-between align-items-end">` +
            `<p class="price"><i class="fas fa-indian-rupee-sign"></i>${price}</p>` +
            `</div>` +
            `<?php if ($role === 'customer'): ?>` +
            `<a href="../users-profile.php?get_id=${service.id}#overview" class="delete-btn btn btn-success">Visit Profile</a>` +
            `<?php endif; ?>` +
            `</form>` +
            `</div>`+
            `</div>`
        );
    });

    const rows = [];
    for (let i = 0; i < serviceHtml.length; i += 4) {
        rows.push(`<div class="row">${serviceHtml.slice(i, i + 4).join('')}</div>`);
    }

    document.getElementById('root').innerHTML = rows.join('');
};

document.getElementById('searchBar').addEventListener('keyup', (e) => {
    const searchData = e.target.value.toLowerCase();
    const filteredData = services.filter((service) => {
        return service.name.toLowerCase().includes(searchData);
    });
    displayService(filteredData);
});

displayService(services);

document.getElementById('searchByLocationBtn').addEventListener('click', () => {
    const locationInput = document.getElementById('locationSearch').value.trim().toLowerCase();

    const filteredData = services.filter((service) => {
        return service.location.toLowerCase().includes(locationInput);
    });

    displayService(filteredData);
});
</script>
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
