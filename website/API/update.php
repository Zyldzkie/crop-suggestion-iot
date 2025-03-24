<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_GET['u']) ? $_GET['u'] : '';

    switch ($action) {
        case '1sthalf':
            $id = $_POST['id'];
            $price = $_POST['price'];

            // Basic validation
            if (empty($id) || empty($price)) {
                echo "All fields are required.";
            } else {
                // Update query (Only update the price)
                $sql = "UPDATE average_price_first_half SET price = '$price' WHERE id = '$id'";

                if ($conn->query($sql) === TRUE) {
                    echo "Record updated successfully.";
                    header("Location: /1sthalf?status=changed");
                } else {
                    echo "Error updating record: " . $conn->error;
                }
            }
            break;

        case '2ndhalf':
            $id = $_POST['id'];
            $price = $_POST['price'];

            // Basic validation
            if (empty($id) || empty($price)) {
                echo "All fields are required.";
            } else {
                // Update query (Only update the price)
                $sql = "UPDATE average_price_second_half SET price = '$price' WHERE id = '$id'";

                if ($conn->query($sql) === TRUE) {
                    echo "Record updated successfully.";
                    header("Location: /2ndhalf?status=changed");
                } else {
                    echo "Error updating record: " . $conn->error;
                }
            }
            break;

        case 'crop':
            $id = $_POST['id'];
            $name = $_POST['name'];

            // Basic validation
            if (empty($id) || empty($name)) {
                echo "All fields are required.";
            } else {
                // Update query (Only update the price)
                $sql = "UPDATE crop SET name = '$name' WHERE id = '$id'";

                if ($conn->query($sql) === TRUE) {
                    echo "Record updated successfully.";
                    header("Location: /crop?status=changed");
                } else {
                    echo "Error updating record: " . $conn->error;
                }
            }
            break;

        case 'crop_utilization':
            $id = $_POST['id'];
            $crop_utilization = $_POST['crop_utilization'];

            // Basic validation
            if (empty($id) || empty($crop_utilization)) {
                echo "All fields are required.";
            } else {
                // Update query (Only update the price)
                $sql = "UPDATE crop_utilization SET utilization_gm_per_day = '$crop_utilization' WHERE id = '$id'";

                if ($conn->query($sql) === TRUE) {
                    echo "Record updated successfully.";
                    header("Location: /crop_utilization?status=changed");
                } else {
                    echo "Error updating record: " . $conn->error;
                }
            }
            break;

        case 'ph_requirements':
            $id = $_POST['id'];
            $min_pH = $_POST['min_pH'];
            $max_pH = $_POST['max_pH'];

            // Basic validation
            if (empty($id) || empty($min_pH) || empty($max_pH)) {
                echo "All fields are required.";
            } else {
                // Update query (Only update the price)
                $sql = "UPDATE ph_level_requirements SET min_pH = '$min_pH', max_pH = '$max_pH'  WHERE id = '$id'";

                if ($conn->query($sql) === TRUE) {
                    echo "Record updated successfully.";
                    header("Location: /ph_requirements?status=changed");
                } else {
                    echo "Error updating record: " . $conn->error;
                }
            }
            break;

        case 'time_of_planting':
            $id = $_POST['id'];
            $start_month = $_POST['start_month'];
            $end_month = $_POST['end_month'];

            // Basic validation
            if (empty($id) || empty($start_month) || empty($end_month)) {
                echo "All fields are required.";
            } else {
                // Update query (Only update the price)
                $sql = "UPDATE time_of_planting SET start_month = '$start_month', end_month = '$end_month'  WHERE id = '$id'";

                if ($conn->query($sql) === TRUE) {
                    echo "Record updated successfully.";
                    header("Location: /time_of_planting?status=changed");
                } else {
                    echo "Error updating record: " . $conn->error;
                }
            }
            break;

        case 'not_in_season':
            $id = $_POST['id'];
            $start_month = $_POST['start_month'];
            $end_month = $_POST['end_month'];

            // Basic validation
            if (empty($id) || empty($start_month) || empty($end_month)) {
                echo "All fields are required.";
            } else {
                // Update query (Only update the price)
                $sql = "UPDATE time_of_planting_not_in_season SET start_month = '$start_month', end_month = '$end_month'  WHERE id = '$id'";

                if ($conn->query($sql) === TRUE) {
                    echo "Record updated successfully.";
                    header("Location: /not_in_season?status=changed");
                } else {
                    echo "Error updating record: " . $conn->error;
                }
            }
            break;

        default:
            break;

    }
}

$conn->close();
?>
