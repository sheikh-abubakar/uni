<?php
// Include your config file for the database connection
include 'config1.php'; // Make sure this path is correct

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_code = $_POST['class_code'];
    $crs_code = $_POST['crs_code'];
    $prof_id = $_POST['prof_id'];
    $room_code = $_POST['room_code'];

    // Insert the class into the database
    $sql = "INSERT INTO CLASS (CLASS_CODE, CRS_CODE, PROF_ID, ROOM_CODE) VALUES ('$class_code', '$crs_code', '$prof_id', '$room_code')";

    if ($conn->query($sql) === TRUE) {
        echo "New class created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}