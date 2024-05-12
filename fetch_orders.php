<?php
// Include your database connection file
include 'components/connect.php';

// Query to fetch orders
$query = "SELECT * FROM orders ORDER BY order_id DESC"; // Assuming 'orders' is your table name
$result = mysqli_query($conn, $query);

// Fetch data and store in array
$orders = array();
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($orders);
?>
