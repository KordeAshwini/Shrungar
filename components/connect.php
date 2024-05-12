<?php

// Start session
//session_start();

// Database connection details for MySQLi
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "loginsystem";

// Establishing connection using MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);

// Function to generate unique IDs
function create_unique_id(){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 20; $i++) {
        $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Check if user is logged in
if(isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to retrieve user details from login table
    $sql = "SELECT * FROM login WHERE userId = $user_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch user details
        $user = $result->fetch_assoc();

        // Now you have the user details, you can fetch additional information from other tables using their ID

        // Fetch user's profile info from users table
        $sql_profile = "SELECT * FROM users WHERE user_id = $user_id";
        $result_profile = $conn->query($sql_profile);
        $profile = $result_profile->fetch_assoc();

        // Fetch user's appointments
        $sql_appointments = "SELECT * FROM appointments WHERE user_id = $user_id";
        $result_appointments = $conn->query($sql_appointments);
        // Process appointments...
        
        // Fetch user's cart items
        $sql_cart = "SELECT * FROM cart WHERE user_id = $user_id";
        $result_cart = $conn->query($sql_cart);
        // Process cart items...

        // Fetch user's orders
        $sql_orders = "SELECT * FROM orders WHERE user_id = $user_id";
        $result_orders = $conn->query($sql_orders);
        // Process orders...

        // Fetch user's products (if they are a business person)
        if ($user['role'] == 'business') {
            $sql_products = "SELECT * FROM products WHERE user_id = $user_id";
            $result_products = $conn->query($sql_products);
            // Process products...
        }

        // Fetch user's services (if they are a business person)
        if ($user['role'] == 'business') {
            $sql_services = "SELECT * FROM services WHERE user_id = $user_id";
            $result_services = $conn->query($sql_services);
            // Process services...
        }

    } else {
        echo "User not found!";
    }

    // Close connection
   // $conn->close();
} else {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit();
}

?>
