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

//echo json_encode(["success" => "pH level recorded successfully", "pH_level" => $pH_level]);

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

//echo json_encode(["success" => "pH level recorded successfully", "pH_level" => $pH_level, "Crops" => $crops]);

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
    //echo json_encode(["success" => "Month recorded successfully", "Crops" => $crops_by_month]);
} else {
    echo json_encode(["error" => "ERROR", "Message" => "NO Plants can be planted this month"]);
}

////////////////////////////////////////////////
//END LEVEL TWO - MONTH BASED margin of 14Days//
////////////////////////////////////////////////


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


//////////////////////////////////////////
//START LEVEL THREE - Utilization BASED//
/////////////////////////////////////////

$required = 5;
$crops_final = [];

// Check if $crops_by_month is not empty
if (!empty($crops_by_month)) {
    $crop_ids = array_column($crops_by_month, 'crop_id');

    // Prepare the placeholders for the SQL query
    if (!empty($crop_ids)) {
        $placeholders = implode(',', array_fill(0, count($crop_ids), '?'));

        // SQL query to fetch the crops based on the crop_ids
        $sql = "SELECT *
                FROM crop_utilization u
                JOIN crop c ON c.id = u.crop_id 
                WHERE u.crop_id IN ($placeholders)
                ORDER BY u.utilization_gm_per_day DESC
                LIMIT ?"; // Limiting the results based on $required

        $stmt = $conn->prepare($sql);

        // Bind the parameters dynamically
        $types = str_repeat("i", count($crop_ids)) . "i"; // Binding types for the crop_ids and required
        $stmt->bind_param($types, ...array_merge($crop_ids, [$required]));

        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch and store the results
        while ($row = $result->fetch_assoc()) {
            $crops_final[] = [
                'name' => $row['name'],
                'crop_id' => $row['crop_id']
            ];
        }

        // Fill with "N/A" if fewer results than required
        while (count($crops_final) < $required) {
            $crops_final[] = [
                'name' => 'N/A',
                'crop_id' => 'N/A'
            ];
        }
    }
}

    //echo json_encode(["success" => "Final recorded successfully", "Suggested Crops 1" => $crops_final]);


//////////////////////////////////////////
//END LEVEL THREE - Utilization BASED//
/////////////////////////////////////////


//2nd SUGGESTION//


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

$crops2 = [];
while ($row = $result->fetch_assoc()) {
    $crops2[] = [$row['name'], $row['crop_id']];
}

$stmt->close();

//echo json_encode(["success" => "pH level recorded successfully", "pH_level" => $pH_level, "Crops" => $crops]);

////////////////////////////////////////////
//END LEVEL ONE - PH BASED margin of .03////
////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////
//START LEVEL TWO - MONTH BASED margin of 14Days//
//////////////////////////////////////////////////

$today2 = new DateTime();

$crops_by_month2 = [];

foreach ($crops2 as $crop) {
    $search_crop_id = $crop[1];


    $sql = "SELECT *
            FROM time_of_planting_not_in_season t
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
            $crops_by_month2[] = ['name' => $name, 'crop_id' => $crop_id];
        } else {
            $start_date = DateTime::createFromFormat('F', $start_month);
            $end_date = DateTime::createFromFormat('F', $end_month);

            $start_date->modify("-14 days");
            $end_date->modify("+14 days");

            if ($today2 >= $start_date && $today <= $end_date) {
                $crops_by_month2[] = ['name' => $name, 'crop_id' => $crop_id];
            }
        }
    }
}

if (!empty($crops_by_month2)) {
    //echo json_encode(["success" => "Month recorded successfully", "Crops" => $crops_by_month2]);
} else {
    echo json_encode(["error" => "ERROR", "Message" => "NO Plants can be planted this month"]);
}

////////////////////////////////////////////////
//END LEVEL TWO - MONTH BASED margin of 14Days//
////////////////////////////////////////////////


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


//////////////////////////////////////////
//START LEVEL THREE - Utilization BASED//
/////////////////////////////////////////

$required2 = 5;
$crops_final2 = [];

$currentMonth = date('n'); 

// Determine the landed month and corresponding table
$landedMonth = ($currentMonth + 3) % 12;
if ($landedMonth == 0) {
    $landedMonth = 12;
}

$table = ($landedMonth >= 1 && $landedMonth <= 6) ? 'average_price_first_half' : 'average_price_second_half';

if (!empty($crops_by_month2)) {
    $crop_ids2 = array_column($crops_by_month2, 'crop_id');

    if (!empty($crop_ids2)) {
        $placeholders = implode(',', array_fill(0, count($crop_ids2), '?'));

        $sql = "SELECT *
                FROM $table p
                JOIN crop c ON c.id = p.crop_id 
                WHERE p.crop_id IN ($placeholders)
                ORDER BY p.price DESC
                LIMIT ?"; 

        $stmt = $conn->prepare($sql);

        $types = str_repeat("i", count($crop_ids2)) . "i"; 
        $stmt->bind_param($types, ...array_merge($crop_ids2, [$required2]));

        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $crops_final2[] = [
                'name' => $row['name'],
                'crop_id' => $row['crop_id']
            ];
        }

        // Fill with "N/A" if fewer results than required
        while (count($crops_final2) < $required2) {
            $crops_final2[] = [
                'name' =>'N/A',
                'crop_id' => 'N/A'
            ];
        }
    }
}

// Ensure $crops_final exists before using it
$crops_final = isset($crops_final) ? $crops_final : [];

echo json_encode([
    "success" => "Final recorded successfully", 
    "Suggested Crops 1" => $crops_final, 
    "Suggested Crops 2" => $crops_final2
]);


$conn->close();


//////////////////////////////////////////
//END LEVEL THREE - Utilization BASED//
/////////////////////////////////////////





?>
