<?php
session_start();
// Include the database connection file
include './includes/usersdb_conn.php';

// Check if user_id exists in the session
if (isset($_SESSION['user_id'])) {
    // Get user_id from the session
    $user_id = $_SESSION['user_id'];
    //echo "Welcome, User ID: $user_id";

    // Fetch user details from the database or session
    if (isset($_SESSION['user_details'])) {
        // Fetch user details from the session
        $userDetails = $_SESSION['user_details'];
    } else {
        // Fetch user details from the database
        $fetchUserQuery = "SELECT * FROM users WHERE user_id = ?";
        $fetchUserStmt = $conn->prepare($fetchUserQuery);
        $fetchUserStmt->bind_param("i", $user_id);
        $fetchUserStmt->execute();
        $userResult = $fetchUserStmt->get_result();

        // Check if user details are fetched
        if ($userResult->num_rows > 0) {
            // Store user details in the session for later use
            $_SESSION['user_details'] = $userResult->fetch_assoc();
            $userDetails = $_SESSION['user_details'];
        } else {
            // Initialize user details in the session
            $_SESSION['user_details'] = array(
                'fullName' => '',
                'address' => '',
                'phone' => '',
                'email' => '',
                'service' => ''
            );
            $userDetails = $_SESSION['user_details'];
        }
    }
} else {
    // Redirect the user to the login page or another page for authentication
    header("Location: login.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle profile update
    if (isset($_POST['submitProfile'])) {
        // Retrieve form data
        $fullName = $_POST['fullName'] ?? '';
        $address = $_POST['address'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $email = $_POST['email'] ?? '';
        $service = $_POST['service'] ?? '';

        // Check if the user already has a profile in the database
        $checkUserQuery = "SELECT * FROM users WHERE user_id = ?";
        $checkUserStmt = $conn->prepare($checkUserQuery);
        $checkUserStmt->bind_param("i", $user_id);
        $checkUserStmt->execute();
        $userResult = $checkUserStmt->get_result();

        if ($userResult->num_rows > 0) {
            // Update user profile in the database
            $updateUserQuery = "UPDATE users SET fullName = ?, address = ?, phone = ?, email = ?, service = ? WHERE user_id = ?";
            $updateUserStmt = $conn->prepare($updateUserQuery);
            $updateUserStmt->bind_param("sssssi", $fullName, $address, $phone, $email, $service, $user_id);

            if ($updateUserStmt->execute()) {
                // Update user details in the session
                $_SESSION['user_details']['fullName'] = $fullName;
                $_SESSION['user_details']['address'] = $address;
                $_SESSION['user_details']['phone'] = $phone;
                $_SESSION['user_details']['email'] = $email;
                $_SESSION['user_details']['service'] = $service;

                // Redirect to the user profile page or display a success message
                header("Location: users-profile.php");
                exit();
            } else {
                echo "Error updating user record: " . $updateUserStmt->error;
            }
        } else {
            // Insert new user profile into the database
            $insertUserQuery = "INSERT INTO users (user_id, fullName, address, phone, email, service) VALUES (?, ?, ?, ?, ?, ?)";
            $insertUserStmt = $conn->prepare($insertUserQuery);
            $insertUserStmt->bind_param("isssss", $user_id, $fullName, $address, $phone, $email, $service);

            if ($insertUserStmt->execute()) {
                // Update user details in the session
                $_SESSION['user_details']['fullName'] = $fullName;
                $_SESSION['user_details']['address'] = $address;
                $_SESSION['user_details']['phone'] = $phone;
                $_SESSION['user_details']['email'] = $email;
                $_SESSION['user_details']['service'] = $service;

                // Redirect to the user profile page or display a success message
                header("Location: users-profile.php");
                exit();
            } else {
                echo "Error inserting new user record: " . $insertUserStmt->error;
            }
        }
    }


    if (isset($_POST['submitAppointment'])) {

        // Retrieve form data
        $appointmentFullName = $_POST['name'] ?? '';
        $appointmentPhone = $_POST['phnumber'] ?? '';
        $appointmentEmail = $_POST['emailid'] ?? '';
        $appointmentDate = $_POST['appointmentDate'] ?? '';
        $appointmentTime = $_POST['appointmentTime'] ?? '';
        $appointmentCity = $_POST['city'] ?? '';
        $appointmentPlace = $_POST['place'] ?? '';
        $appointmentService = $_POST['service'] ?? '';
    
        // Insert data into the appointments table
        $appointmentSql = $conn->prepare("INSERT INTO appointments (user_id, name, phnumber, emailid, appointmentDate, appointmentTime, city, place, service) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        echo "Session User ID: " . $_SESSION['user_id'] . "<br>";
        // Bind parameters
        $appointmentSql->bind_param("issssssss", $_SESSION['user_id'], $appointmentFullName, $appointmentPhone, $appointmentEmail, $appointmentDate, $appointmentTime, $appointmentCity, $appointmentPlace, $appointmentService);
    
        // Execute the statement
        if ($appointmentSql->execute()) {
            // Redirect or display a success message
            header("Location: success_page.php"); // Change this to the desired page
            exit();
        } else {
            echo "Error inserting appointment record: " . $conn->error;
        }
    }
}
/*if (isset($_SESSION['user_details'])) {
    $userDetails = $_SESSION['user_details'];
} else {
    // Handle the case where user details are not found in the session
    echo "User details not found in session.";
    exit();
}*/

// Fetch user details from the database or from the session
if (isset($_SESSION['user_details'])) {
    $userDetails = $_SESSION['user_details'];
} else {
    // Initialize user details if not set
    $userDetails = array(
        'fullName' => '',
        'address' => '',
        'phone' => '',
        'email' => '',
        'service' => ''
    );
}

// Fetch services from the users table (assuming 'service' column contains comma-separated values)
$sql = "SELECT service FROM users WHERE service IS NOT NULL";
$result = $conn->query($sql);

$services = array();

if ($result->num_rows > 0) {
    // Fetching services and storing them in an array
    while($row = $result->fetch_assoc()) {
        // Split the service string by commas
        $split_services = explode(',', $row['service']);
        // Trim each service to remove leading and trailing spaces
        $trimmed_services = array_map('trim', $split_services);
        // Add each trimmed service to the $services array
        $services = array_merge($services, $trimmed_services);
    }
}

// Remove duplicates and empty values from the services array
$services = array_values(array_unique(array_filter($services)));

// Close the database connection (if you have opened it)
//mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Include your head content, such as meta tags, CSS links, etc. -->
    <?php include './includes/head.php'; ?>
</head>

<body>
<div id="pre-loader">  </div>
    <!-- Include your header content -->
    <?php include './includes/header.php'; ?>

    <main id="main" class="main">
        <!-- Your existing HTML content -->
        <div class="all-title-box">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>User Profile</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item active">Profile</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Details -->
        <section class="section profile">
            <div class="row">
                <div class="col-xl-2">
                    <div class="card">
                        <div>
                            <!-- Add any content you want to display in the left column -->
                        </div>
                    </div>
                </div>
                <!-- Display user details based on role -->
             
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-body pt-3">
                            <!-- Bordered Tabs -->
                            <ul class="nav nav-tabs nav-tabs-bordered">
							<?php if ($_SESSION['role'] == 'business' || $_SESSION['role'] == 'customer') : ?>
                                <li class="nav-item">
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
                                </li>
								<?php endif; ?>
								<?php if ($_SESSION['role'] == 'business') : ?>
                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profile</button>
                                </li>
								<?php endif; ?>
								<?php if ($_SESSION['role'] == 'customer') : ?>
                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#appointment">Book Appointment</button>
                                </li>
								<?php endif; ?>
								<?php if ($_SESSION['role'] == 'business') : ?>
                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#uploadproducts">Upload your Products/Services</button>
                                </li>
								<?php endif; ?>
								<?php if ($_SESSION['role'] == 'business') : ?>
								<li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#view-appointments">View Appointments</button>
                                </li>
								<?php endif; ?>
                                <?php if ($_SESSION['role'] == 'business') : ?>
								<li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#view-orders">View Orders</button>
                                </li>
								<?php endif; ?>
                            </ul>
                            <div class="tab-content pt-2">
                                <!-- Profile Overview -->
                                <div class="tab-pane fade show active profile-overview" id="profile-overview">
                                <?php
                                // Assuming $userDetails['role'] contains the role of the user (customer or business)
                                if ($_SESSION['role'] == 'business') {

                               ?>
                                    <!-- Display user details from the session variable -->
                                    <h5 class="card-title">Profile Details</h5>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Full Name</div>
                                        <div class="col-lg-9 col-md-8"><?php echo isset($userDetails['fullName']) ? $userDetails['fullName'] : ''; ?></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Email</div>
                                        <div class="col-lg-9 col-md-8"><?php echo isset($userDetails['email']) ? $userDetails['email'] : ''; ?></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Phone</div>
                                        <div class="col-lg-9 col-md-8"><?php echo isset($userDetails['phone']) ? $userDetails['phone'] : ''; ?></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Address</div>
                                        <div class="col-lg-9 col-md-8"><?php echo isset($userDetails['address']) ? $userDetails['address'] : ''; ?></div>
                                    </div>
                                    <?php } else { ?>
                                        <h5 class="card-title">Profile Details</h5>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Full Name</div>
                                        <div class="col-lg-9 col-md-8">Ashwini Korde</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Email</div>
                                        <div class="col-lg-9 col-md-8">ashwinikorde2805@gmail.com</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Phone</div>
                                        <div class="col-lg-9 col-md-8">9763801422</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Address</div>
                                        <div class="col-lg-9 col-md-8">Susgaon,Pune 411021</div>
                                    </div>
                                    <?php } ?>
                                </div>

                                <div class="tab-pane fade profile-edit pt-3" id="profile-edit">
                                    <!-- Profile Edit Form -->
                                    <form method="post" action="">
                                        <h5 class="card-title">Edit your Profile</h5>
                                        <div class="row mb-3">
                                            <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Full Name</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="fullName" type="text" class="form-control" id="fullName" value="<?php echo isset($userDetails['fullName']) ? $userDetails['fullName'] : ''; ?>">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="address" class="col-md-4 col-lg-3 col-form-label">Address</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="address" type="text" class="form-control" id="address" value="<?php echo isset($userDetails['address']) ? $userDetails['address'] : ''; ?>">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="phone" class="col-md-4 col-lg-3 col-form-label">Phone</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="phone" type="text" class="form-control" id="phone" value="<?php echo isset($userDetails['phone']) ? $userDetails['phone'] : ''; ?>">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="email" type="email" class="form-control" id="email" value="<?php echo isset($userDetails['email']) ? $userDetails['email'] : ''; ?>">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="service" class="col-md-4 col-lg-3 col-form-label">Mention your services</label>
                                            <div class="col-md-8 col-lg-9">
                                                <input name="service" type="text" class="form-control" id="service" value="<?php echo isset($userDetails['service']) ? $userDetails['service'] : ''; ?>">
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary" name="submitProfile">Save Changes</button>
                                        </div>
                                    </form><!-- End Profile Edit Form -->
                                </div>

                              <!-- Appointment Booking Form -->
<div class="tab-pane fade appointment-book pt-3" id="appointment">
    <form method="post" action="process_form.php">
        <h5 class="card-title">Book Your Appointment</h5>
        <div class="row mb-3">
            <label for="name" class="col-md-4 col-lg-3 col-form-label">Full Name</label>
            <div class="col-md-8 col-lg-9">
                <input name="name" type="text" class="form-control" id="name">
            </div>
        </div>

        <div class="row mb-3">
            <label for="phnumber" class="col-md-4 col-lg-3 col-form-label">Phone</label>
            <div class="col-md-8 col-lg-9">
                <input name="phnumber" type="text" class="form-control" id="phnumber">
            </div>
        </div>

        <div class="row mb-3">
            <label for="emailid" class="col-md-4 col-lg-3 col-form-label">Email</label>
            <div class="col-md-8 col-lg-9">
                <input name="emailid" type="email" class="form-control" id="emailid">
            </div>
        </div>

        <!-- Add Date and Time Picker -->
        <div class="row mb-3">
            <label for="appointmentDate" class="col-md-4 col-lg-3 col-form-label">Appointment Date</label>
            <div class="col-md-4 col-lg-3">
                <input name="appointmentDate" type="date" class="form-control" id="appointmentDate">
            </div>

            <label for="appointmentTime" class="col-md-4 col-lg-2 col-form-label">Appointment Time</label>
            <div class="col-md-4 col-lg-4">
                <!-- Replace input field with dropdown for time slots -->
                <select name="appointmentTime" class="form-control" id="appointmentTime">
                    <option value="09:00 AM">09:00 AM</option>
                    <option value="10:00 AM">10:00 AM</option>
                    <option value="11:00 AM">11:00 AM</option>
                    <!-- Add more time slots as needed -->
                </select>
            </div>
        </div>

        <!-- Add Cities Dropdown with Places -
        <div class="row mb-3">
            <label for="city" class="col-md-4 col-lg-3 col-form-label">City</label>
            <div class="col-md-8 col-lg-9">
                <select name="city" class="form-control" id="city">
                    <option value="city1">City 1</option>
                    <option value="city2">City 2</option>
                    <-- Add more cities as needed --
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <label for="place" class="col-md-4 col-lg-3 col-form-label">Place</label>
            <div class="col-md-8 col-lg-9">
                <select name="place" class="form-control" id="place">
                    <option value="place1">Place 1</option>
                    <option value="place2">Place 2</option>
                    <-- Add more places as needed --
                </select>
            </div>
        </div>-->
        <!-- Add Cities Dropdown with Places -->
        <div class="row mb-3">
    <label for="city" class="col-md-4 col-lg-3 col-form-label">City</label>
    <div class="col-md-8 col-lg-9">
        <select name="city" class="form-control" id="city" >
        <option value="" disabled selected>Select City</option>
        <option value="Mumbai">Mumbai</option>
<option value="Pune">Pune</option>
<option value="Nagpur">Nagpur</option>
<option value="Nashik">Nashik</option>
<option value="Aurangabad">Aurangabad</option>
<option value="Solapur">Solapur</option>
<option value="Thane">Thane</option>
<option value="Amravati">Amravati</option>
<option value="Kolhapur">Kolhapur</option>
<option value="Sangli">Sangli</option>
<!-- Add more cities as needed -->

        </select>
    </div>
</div>

<div class="row mb-3">
    <label for="place" class="col-md-4 col-lg-3 col-form-label">Place</label>
    <div class="col-md-8 col-lg-9">
        <select name="place" class="form-control" id="place">
           
        <script>
    // Define the places for each city
    const places = {
    Mumbai: ["Bandra", "Andheri", "Colaba", "Dadar", "Powai"],
    Pune: ["Koregaon Park", "Shivajinagar", "Kothrud", "Hadapsar", "Baner"],
    Nagpur: ["Dharampeth", "Sadar", "Sitabuldi", "Dhantoli", "Ramdaspeth"],
    Nashik: ["Panchavati", "Indira Nagar", "Satpur", "Ambad", "Govind Nagar"],
    Aurangabad: ["CIDCO", "Garkheda", "Jalna Road", "Nirala Bazaar", "Gangapur"],
    Solapur: ["Solapur City Center", "Balives", "Hotgi Road", "Kegaon", "Sukhdeo Nagar"],
    Thane: ["Vartak Nagar", "Kopri", "Teen Hath Naka", "Wagle Estate", "Ghodbunder Road"],
    Amravati: ["Rajapeth", "Badnera", "Rajkamal Chowk", "Shegaon Naka", "Wanjarwada"],
    Kolhapur: ["Shivaji Park", "Rajarampuri", "Shahupuri", "Tarabai Park", "Gandhi Nagar"],
    Sangli: ["Vishrambag", "Vijaynagar", "Gaon Bhag", "Miraj Road", "Siddheshwar Peth"]
    // Add more places for each city as needed
};


    // Function to update the options in the "Place" dropdown based on the selected city
    function updatePlaces() {
        const citySelect = document.getElementById("city");
        const placeSelect = document.getElementById("place");
        const selectedCity = citySelect.value;
        placeSelect.innerHTML = ""; // Clear existing options
        places[selectedCity].forEach(place => {
            const option = document.createElement("option");
            option.value = place;
            option.textContent = place;
            placeSelect.appendChild(option);
        });
    }

    // Add event listener to the "City" dropdown to trigger update of "Place" dropdown
    document.getElementById("city").addEventListener("change", updatePlaces);

    // Initial population of "Place" dropdown based on default selected city
    updatePlaces();
</script>


        </select>
    </div>
</div>

        <div class="row mb-3">
    <label for="service" class="col-md-4 col-lg-3 col-form-label">Service</label>
    <div class="col-md-8 col-lg-9">
        <select name="service" class="form-control" id="service">
            <?php
            // Loop through the services array and populate the dropdown options
            foreach ($services as $service) {
                echo '<option value="' . htmlspecialchars($service) . '">' . htmlspecialchars($service) . '</option>';
            }
            ?>
        </select>
    </div>
</div>


        <div class="text-center">
            <button type="submit" class="btn btn-primary" name="submitAppointment" >Book Appointment</button>
        </div>
       
    </form><!-- End Appointment Booking Form -->
</div>

                                  <div class="tab-pane fade profile-edit pt-3" id="uploadproducts">
                                    <!-- Display user details from the session variable -->
                                    <h5 class="card-title">Upload your Products/Services</h5>
                                    
                                    <?php

   include_once 'components/connect.php';

    /*if(isset($_COOKIE['user_id'])){
        $user_id = $_COOKIE['user_id'];
    } else {
        setcookie('user_id', create_unique_id(), time() + 60*60*24*30);
    }*/
    if(isset($_SESSION['user_id'])) {
        // Get the user ID from the session
        $user_id = $_SESSION['user_id'];
    
        // Now you can use $user_id as needed, for example:
        //echo "Welcome, User ID: $user_id";
    } else {
        // Redirect the user to the login page if not logged in
        header("Location: login.php");
        exit(); // Ensure no further code execution after redirection
    }

    if(isset($_POST['add'])){
        //$id = create_unique_id();
        $name = $_POST['name'];
        $name = filter_var($name, FILTER_SANITIZE_STRING);
        $type = $_POST['type'];
        $type = filter_var($type, FILTER_SANITIZE_STRING);
        $price = $_POST['price'];
        $price = filter_var($price, FILTER_SANITIZE_STRING);
        $image = $_FILES['image']['name'];
        $image = filter_var($image, FILTER_SANITIZE_STRING);
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $rename = create_unique_id().'.'.$ext;
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_size = $_FILES['image']['size'];
        $image_folder = 'project/uploaded_files/'.$rename;

        if($image_size > 2000000){
            $warning_msg[] = 'Image size is too large!';
        } else {
            if($type == 'products') {
                // Add product without location
                $add_product = $conn->prepare("INSERT INTO `products`(user_id, name, price, image) VALUES(?,?,?,?)");
                //$add_product->execute([$user_id,$name, $price, $rename]);
                if (!$add_product) {
                    die('Error in prepare statement: ' . $conn->error);
                }
                
                $add_product->bind_param("isss", $user_id, $name, $price, $rename);
                $add_product->execute();
                move_uploaded_file($image_tmp_name, $image_folder);
                $success_msg[] = 'Product added!';
            } elseif($type == 'services') {
                $location = $_POST['location']; // New field for services
                $location = filter_var($location, FILTER_SANITIZE_STRING);
                $add_service = $conn->prepare("INSERT INTO `services` (user_id, name, price, image, location) VALUES (?, ?, ?, ?, ?)");
                if (!$add_service) {
                    die('Error in prepare statement: ' . $conn->error);
                }
                
                $add_service->bind_param("issss", $user_id, $name, $price, $rename, $location);
                $add_service->execute();
                move_uploaded_file($image_tmp_name, $image_folder);
                $success_msg[] = 'Service added!';
            } else {
                $warning_msg[]= 'Product not added!';
            }
    }     
    }
    ?>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">

<link rel="stylesheet" href="css/style.css">



<section class="product-form">
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="row mb-3">
            <label for="" class="col-md-4 col-lg-3 col-form-label">Product/Service name</label>
            <div class="col-md-8 col-lg-9">
                <input type="text" name="name" placeholder="Enter product/Service name" class="form-control box" required>
            </div>
        </div>
        <div class="row mb-3">
            <label for="" class="col-md-4 col-lg-3 col-form-label">Select type</label>
            <div class="col-md-8 col-lg-9">
                <select name="type" class="form-control box" required>
                    <option value="products">Products</option>
                    <option value="services">Services</option>
                    <!-- Add more cities as needed -->
                </select>
            </div>
        </div>
        
        <div class="row mb-3">
            <label for="" class="col-md-4 col-lg-3 col-form-label">Product price</label>
            <div class="col-md-8 col-lg-9">
                <input type="number" name="price" placeholder="Enter product price" class="form-control box" required>
            </div>
        </div>
        <div class="row mb-3">
            <label for="" class="col-md-4 col-lg-3 col-form-label">Upload image</label>
            <div class="col-md-8 col-lg-9">
                <input type="file" name="image" required accept="image/*" class="form-control box" required>
            </div>
        </div>
        <div class="row mb-3" id="location_field" style="display: none;">
            <label for="" class="col-md-4 col-lg-3 col-form-label">Location</label>
            <div class="col-md-8 col-lg-12">
                <input type="text" name="location" placeholder="Enter service location" class="form-control box">
            </div>
        </div>
    <div class="text-center">
        <button type="submit" name="add" class="btn btn-primary">Submit</button>
    </div>
    </form>
</section>

	 <!--<script>
        // Script to show/hide location field based on product type selection
        const typeSelect = document.querySelector('select[name="type"]');
        const locationField = document.getElementById('location_field');

        typeSelect.addEventListener('change', function() {
            if (this.value === 'services') {
                locationField.style.display = 'block';
            } else {
                locationField.style.display = 'none';
            }
        });
    </script>-->
	<script>
    // Script to show/hide location field based on product type selection
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.querySelector('select[name="type"]');
        const locationField = document.getElementById('location_field');

        typeSelect.addEventListener('change', function() {
            locationField.style.display = this.value === 'services' ? 'block' : 'none';
        });
    });
</script>



</div>

<!-- View Appointments Section -->
<div class="tab-pane fade view-appointments pt-3" id="view-appointments">
    <h5 class="card-title">View Appointments</h5>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>Email</th>
                    <th>Appointment Date</th>
                    <th>Time</th>
                    <th>City</th>
                    <th>Place</th>
                    <th>Service</th>
                    <th>Booked on</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Include the database connection file
                include './includes/usersdb_conn.php';

                // Fetch appointments from the database
                $sql = "SELECT * FROM appointments";
                $result = $conn->query($sql);

                // Check if there are appointments
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        
                        echo "<td>" . $row["name"] . "</td>";
                        echo "<td>" . $row["phnumber"] . "</td>";
                        echo "<td>" . $row["emailid"] . "</td>";
                        echo "<td>" . $row["appointmentDate"] . "</td>";
                        echo "<td>" . $row["appointmentTime"] . "</td>";
                        echo "<td>" . $row["city"] . "</td>";
                        echo "<td>" . $row["place"] . "</td>";
                        echo "<td>" . $row["service"] . "</td>";
                        echo "<td>" . $row["created_at"] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>No appointments found.</td></tr>";
                }

                // Close the database connection
                mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>
</div>



<!-- View orders Section -->
<div class="tab-pane fade view-orders pt-3" id="view-orders">
    <h5 class="card-title">View Orders</h5>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Customer ID</th>
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Address Type</th>
                    <th>Payment Method</th>
                    <th>Product ID</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Booked on</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Include the database connection file
                include './includes/usersdb_conn.php';

                // Fetch appointments from the database
                $sql = "SELECT * FROM orders";
                $result = $conn->query($sql);

                // Check if there are appointments
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["user_id"] . "</td>";
                        echo "<td>" . $row["name"] . "</td>";
                        echo "<td>" . $row["number"] . "</td>";
                        echo "<td>" . $row["email"] . "</td>";
                        echo "<td>" . $row["address"] . "</td>";
                        echo "<td>" . $row["address_type"] . "</td>";
                        echo "<td>" . $row["method"] . "</td>";
                        echo "<td>" . $row["product_id"] . "</td>";
                        echo "<td>" . $row["price"] . "</td>";
                        echo "<td>" . $row["qty"] . "</td>";
                        echo "<td>" . $row["date"] . "</td>";
                        echo "<td>" . $row["status"] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>No orders found.</td></tr>";
                }

                // Close the database connection
                mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>
</div>





                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Your existing HTML content -->
    </main>
	
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
	

    <!-- Include your footer content -->
    <?php include './includes/footer.php'; ?>

    <a href="#" id="back-to-top" title="Back to top" style="display: none;">&uarr;</a>

    <!-- Include your JS scripts -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- ALL JS FILES -->
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <!-- ALL PLUGINS -->
    <script src="js/jquery.superslides.min.js"></script>
    <script src="js/bootstrap-select.js"></script>
    <script src="js/inewsticker.js"></script>
    <script src="js/bootsnav.js."></script>
    <script src="js/images-loded.min.js"></script>
    <script src="js/isotope.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/baguetteBox.min.js"></script>
    <script src="js/form-validator.min.js"></script>
    <script src="js/contact-form-script.js"></script>
    <script src="js/custom.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>
    <!-- ... -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<script src="js/script.js"></script>

<?php include 'components/alert.php'; ?>

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
