<?php 

$dbServername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "loginsystem";

$connect = new mysqli($dbServername, $dbUsername, $dbPassword, $dbName);

/*
// Check connection
if ($mysqli -> connect_error) {
  echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
  exit();
}*/
// Check connection
if ($connect->connect_error) {
  die("Connection failed: " . $connect->connect_error);
}

// Optionally, set the character set to utf8mb4
if (!$connect->set_charset("utf8mb4")) {
  printf("Error loading character set utf8mb4: %s\n", $connect->error);
}

?>