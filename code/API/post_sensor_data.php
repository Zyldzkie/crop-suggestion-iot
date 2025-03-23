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
        $sql = "INSERT INTO suggested_crops (reading_id, crop_name, crop_type) 
                VALUES (?, ?, 'gm')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $reading_id, $crop["name"]);
        $stmt->execute();
    }
}

if (isset($data["suggestedCrops2"])) {
    foreach ($data["suggestedCrops2"] as $crop) {
        $sql = "INSERT INTO suggested_crops (reading_id, crop_name, crop_type) 
                VALUES (?, ?, 'price')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $reading_id, $crop["name"]);
        $stmt->execute();
    }
}

// Get suggested crops
$suggestedCrops1 = [];
$suggestedCrops2 = [];

if ($reading_id) {
    // Get GM/day based crops
    $sql = "SELECT * FROM suggested_crops WHERE reading_id = ? AND crop_type = 'gm'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reading_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $suggestedCrops1[] = [
            'name' => $row['crop_name']
        ];
    }
    
    // Get price based crops
    $sql = "SELECT * FROM suggested_crops WHERE reading_id = ? AND crop_type = 'price'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reading_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $suggestedCrops2[] = [
            'name' => $row['crop_name']
        ];
    }
}

$response = [
    "success" => "Sensor data received successfully",
    "reading_id" => $reading_id
];

echo json_encode($response);
?> 