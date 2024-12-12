<?php
include 'config1.php'; // Include the database connection

// Retrieve form data
$class_code = $_POST['class_code'];
$class_date = $_POST['class_date'];
$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO ATTENDANCE (CLASS_CODE, CLASS_DATE, START_TIME, END_TIME) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $class_code, $class_date, $start_time, $end_time);

if ($stmt->execute()) {
    echo "Class scheduled successfully";
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>