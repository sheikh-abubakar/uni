<?php
include 'config1.php'; // Include the database connection

// Retrieve form data
$crs_code = $_POST['crs_code'];
$crs_title = $_POST['crs_title'];
$crs_description = $_POST['crs_description'];
$crs_credits = $_POST['crs_credits'];
$crs_fees = $_POST['crs_fees'];

// Check if the course code already exists
$stmt = $conn->prepare("SELECT * FROM COURSE WHERE CRS_CODE = ?");
$stmt->bind_param("s", $crs_code);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "Error: A course with this course code already exists.";
    $stmt->close();
    $conn->close();
    exit();
}
$stmt->close();

// Insert data into COURSE table
$stmt = $conn->prepare("INSERT INTO COURSE (CRS_CODE, CRS_TITLE, CRS_DESCRIPTION, CRS_CREDITS, CRS_FEES) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssdi", $crs_code, $crs_title, $crs_description, $crs_credits, $crs_fees);

if ($stmt->execute()) {
    echo "New course added successfully";
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>