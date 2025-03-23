<?php
header('Content-Type: application/json');

// Get the latest sensor reading
$sql = "SELECT * FROM sensor_readings ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);
$sensor_data = null;
$reading_id = null;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $sensor_data = [
        'nitrogen' => $row['nitrogen'],
        'phosphorus' => $row['phosphorus'],
        'potassium' => $row['potassium'],
        'pH_level' => $row['pH_level'],
        'timestamp' => $row['timestamp']
    ];
    $reading_id = $row['id'];
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
            'name' => $row['crop_name'],
            'crop_id' => $row['crop_id']
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
            'name' => $row['crop_name'],
            'crop_id' => $row['crop_id']
        ];
    }
}

$response = [
    'sensor_data' => $sensor_data,
    'suggestedCrops1' => $suggestedCrops1,
    'suggestedCrops2' => $suggestedCrops2
];

echo json_encode($response);
?>