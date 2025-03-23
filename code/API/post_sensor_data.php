<?php
header("Content-Type: application/json");

// Check if request method is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => "Invalid request method"]);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (!isset($data["nitrogen"]) || !isset($data["phosphorus"]) || 
    !isset($data["potassium"]) || !isset($data["pH_level"])) {
    echo json_encode(["error" => "Missing required sensor data"]);
    exit;
}

// Store in database instead of session
$sql = "INSERT INTO sensor_readings (nitrogen, phosphorus, potassium, pH_level, timestamp) 
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$timestamp = time();
$stmt->bind_param("ddddi", $data["nitrogen"], $data["phosphorus"], $data["potassium"], $data["pH_level"], $timestamp);
$stmt->execute();

// Get the ID of the inserted reading
$reading_id = $conn->insert_id;

// Store suggested crops if available
if (isset($data["suggestedCrops1"])) {
    foreach ($data["suggestedCrops1"] as $crop) {
        $sql = "INSERT INTO suggested_crops (reading_id, crop_name, crop_id, crop_type) 
                VALUES (?, ?, ?, 'gm')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $reading_id, $crop["name"], $crop["crop_id"]);
        $stmt->execute();
    }
}

if (isset($data["suggestedCrops2"])) {
    foreach ($data["suggestedCrops2"] as $crop) {
        $sql = "INSERT INTO suggested_crops (reading_id, crop_name, crop_id, crop_type) 
                VALUES (?, ?, ?, 'price')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $reading_id, $crop["name"], $crop["crop_id"]);
        $stmt->execute();
    }
}

$response = [
    "success" => "Sensor data received successfully",
    "reading_id" => $reading_id
];

echo json_encode($response);
?> 