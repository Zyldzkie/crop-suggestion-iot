<?php

$dbname = "crop_suggestion";
$dbusername = "root";
$dbpassword = "";
$dbhost = "localhost";

// Create connection
$conn = new mysqli($dbhost, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

?>