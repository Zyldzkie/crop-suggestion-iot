<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crop_suggestion";

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$db_check = $conn->query("SHOW DATABASES LIKE '$dbname'");
if ($db_check->num_rows == 0) {
    // Create database
    $sql = "CREATE DATABASE $dbname";
    if ($conn->query($sql) === TRUE) {
        echo "Database created successfully\n";
    } else {
        die("Error creating database: " . $conn->error);
    }
}

$conn->select_db($dbname);

$tables = [
    "CREATE TABLE IF NOT EXISTS Crop (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL
    )",

    "CREATE TABLE IF NOT EXISTS pH_Level_Requirements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        crop_id INT NOT NULL,
        min_pH DECIMAL(3,1) NOT NULL,
        max_pH DECIMAL(3,1) NOT NULL,
        FOREIGN KEY (crop_id) REFERENCES Crop(id) ON DELETE CASCADE
    )",

    "CREATE TABLE IF NOT EXISTS Time_of_Planting (
        id INT AUTO_INCREMENT PRIMARY KEY,
        crop_id INT NOT NULL,
        start_month VARCHAR(20) NOT NULL,
        end_month VARCHAR(20) NOT NULL,
        FOREIGN KEY (crop_id) REFERENCES Crop(id) ON DELETE CASCADE
    )",

    "CREATE TABLE IF NOT EXISTS Crop_Utilization (
        id INT AUTO_INCREMENT PRIMARY KEY,
        crop_id INT NOT NULL,
        utilization_gm_per_day DECIMAL(6,2) NOT NULL,
        FOREIGN KEY (crop_id) REFERENCES Crop(id) ON DELETE CASCADE
    )",

    "CREATE TABLE IF NOT EXISTS Time_of_Planting_Not_In_Season (
        id INT AUTO_INCREMENT PRIMARY KEY,
        crop_id INT NOT NULL,
        start_month VARCHAR(20) NOT NULL,
        end_month VARCHAR(20) NOT NULL,
        FOREIGN KEY (crop_id) REFERENCES Crop(id) ON DELETE CASCADE
    )",

    "CREATE TABLE IF NOT EXISTS Average_Price_First_Half (
        id INT AUTO_INCREMENT PRIMARY KEY,
        crop_id INT NOT NULL,
        price DECIMAL(6,2) NOT NULL,
        FOREIGN KEY (crop_id) REFERENCES Crop(id) ON DELETE CASCADE
    )",

    "CREATE TABLE IF NOT EXISTS Average_Price_Second_Half (
        id INT AUTO_INCREMENT PRIMARY KEY,
        crop_id INT NOT NULL,
        price DECIMAL(6,2) NOT NULL,
        FOREIGN KEY (crop_id) REFERENCES Crop(id) ON DELETE CASCADE
    )"
];

foreach ($tables as $table) {
    if ($conn->query($table) !== TRUE) {
        echo "Error creating table: " . $conn->error . "\n";
    }
}

$conn->close();
?>