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
        
        default:
            
            break;
    }
}

$conn->close();
?>
