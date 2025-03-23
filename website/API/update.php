<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
}

$conn->close();
?>