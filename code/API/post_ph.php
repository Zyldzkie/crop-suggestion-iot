<?php

header("Content-Type: application/json");

// Check if request method is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => "Invalid request method"]);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["crop_id"]) || !isset($data["pH_level"])) {
    echo json_encode(["error" => "Invalid input"]);
    exit;
}

$crop_id = $data["crop_id"];
$pH_level = $data["pH_level"];

echo json_encode(["success" => "pH level recorded successfully", "crop_id" => $crop_id, "pH_level" => $pH_level]);

$conn->close();

?>
