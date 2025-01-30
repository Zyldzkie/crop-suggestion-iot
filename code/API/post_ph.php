<?php

header("Content-Type: application/json");

// Check if request method is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => "Invalid request method"]);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["pH_level"])) {
    echo json_encode(["error" => "Invalid input"]);
    exit;
}

$pH_level = $data["pH_level"];

echo json_encode(["success" => "pH level recorded successfully", "pH_level" => $pH_level]);

////////////////////////////////////////////
//START LEVEL ONE - PH BASED margin of .03//
////////////////////////////////////////////

$margin = 0.3;
$minPH = $pH_level - $margin;
$maxPH = $pH_level + $margin;

$sql = "
    SELECT * 
    FROM ph_level_requirements plr
    JOIN crop c ON c.id = plr.crop_id
    WHERE plr.min_pH <= ? AND plr.max_pH >= ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("dd", $maxPH, $minPH);
$stmt->execute();
$result = $stmt->get_result();

$crops = [];
while ($row = $result->fetch_assoc()) {
    $crops[] = [$row['name'], $row['crop_id']];
}

$stmt->close();

echo json_encode(["success" => "pH level recorded successfully", "pH_level" => $pH_level, "Crops" => $crops]);

////////////////////////////////////////////
//END LEVEL ONE - PH BASED margin of .03////
////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////
//START LEVEL TWO - MONTH BASED margin of 14Days//
//////////////////////////////////////////////////

$today = new DateTime();

$crops_by_month = [];

foreach ($crops as $crop) {
    $search_crop_id = $crop[1];


    $sql = "SELECT *
            FROM time_of_planting t
            JOIN crop c ON c.id = t.crop_id 
            WHERE t.crop_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $search_crop_id);
    $stmt->execute();
    $result = $stmt->get_result();


    while ($row = $result->fetch_assoc()) {
        $crop_id = $row['crop_id'];
        $name = $row['name'];
        $start_month = $row['start_month'];
        $end_month = $row['end_month'];

        if ($start_month === 'All Season' || $end_month === 'All Season') {
            $crops_by_month[] = ['name' => $name, 'crop_id' => $crop_id];
        } else {
            $start_date = DateTime::createFromFormat('F', $start_month);
            $end_date = DateTime::createFromFormat('F', $end_month);

            $start_date->modify("-14 days");
            $end_date->modify("+14 days");

            if ($today >= $start_date && $today <= $end_date) {
                $crops_by_month[] = ['name' => $name, 'crop_id' => $crop_id];
            }
        }
    }
}

if (!empty($crops_by_month)) {
    echo json_encode(["success" => "Month recorded successfully", "Crops" => $crops_by_month]);
} else {
    echo json_encode(["error" => "ERROR", "Message" => "NO Plants can be planted this month"]);
}

$conn->close();

////////////////////////////////////////////////
//END LEVEL TWO - MONTH BASED margin of 14Days//
////////////////////////////////////////////////


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



?>
