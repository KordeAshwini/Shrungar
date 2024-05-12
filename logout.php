<?php
session_start();
include 'components/connect.php';

// Clear all session variables
$_SESSION = array();
unset($_SESSION["user_id"]); // Uncomment this line if you have specific session variables to unset
session_unset();
// Destroy the session
session_destroy();

// Redirect the user to the login page or any other appropriate page
header("Location: home.php");
exit;
?>
