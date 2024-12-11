<?php
include 'config1.php'; // Include the database connection

// Retrieve form data
$person_id = $_POST['person_id'];
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$email = $_POST['email'];
$dob = $_POST['dob'];
$gender = $_POST['gender'];
$contactno = $_POST['contactno'];
$state_code = $_POST['state_code'];
$city_code = $_POST['city_code'];
$postal_code = $_POST['postal_code'];
$degree = $_POST['degree'];
$prof_id = $_POST['prof_id'];
$dept_code = $_POST['dept_code'];
$password = $_POST['password'];
$img = $_FILES['img']['tmp_name'];

// Check if the username (email) already exists
$stmt = $conn->prepare("SELECT * FROM PERSON WHERE EMAIL = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "Error: A person with this email already exists.";
    $stmt->close();
    $conn->close();
    exit();
}
$stmt->close();

// Insert data into PERSON table
$img_data = addslashes(file_get_contents($img));
$stmt = $conn->prepare("INSERT INTO PERSON (PERSON_ID, FNAME, LNAME, EMAIL, DOB, GENDER, CONTACTNO, STATE_CODE, CITY_CODE, POSTAL_CODE, IMG) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssssss", $person_id, $fname, $lname, $email, $dob, $gender, $contactno, $state_code, $city_code, $postal_code, $img_data);

if ($stmt->execute()) {
    // Insert data into STUDENT table
    $stmt = $conn->prepare("INSERT INTO STUDENT (STU_ID, DEGREE, PROF_ID, DEPT_CODE, PASSWORD) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $person_id, $degree, $prof_id, $dept_code, $password);

    if ($stmt->execute()) {
        echo "New student added successfully";
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
