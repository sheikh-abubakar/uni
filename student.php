<?php
ob_start();
session_start();
require "php/config.php";
require_once "php/functions.php";
$user = new login_registration_class();

$sid = $_SESSION['sid']; // Student ID from session
$sname = $_SESSION['sname']; // Student username (or name) from session

if (!$user->getsession()) {
    header('Location: st_login.php');
    exit();
}

// Fetch student details using the appropriate method
$student_details = $user->getStudentProfile($sid); // $sid is the student ID
$student_data = $student_details->fetch_assoc();

if (!$student_data) {
    echo "Failed to retrieve student details.";
    exit();
}

// Extract necessary data from the result
$full_name = $student_data['FNAME'] . ' ' . $student_data['LNAME'];
$email = $student_data['EMAIL'];
$dob = $student_data['DOB'];
$gender = $student_data['GENDER'];
$degree = $student_data['DEGREE']; // Assuming the degree is now displayed here
$address = $student_data['STATE_CODE'] . ', ' . $student_data['CITY_CODE'] . ', ' . $student_data['POSTAL_CODE'];
?>

<?php 
$pageTitle = "Student Profile";
include "php/headertop.php";
?>

<div class="student_profile">
    <h3 style="text-align:center;color:#fff;margin:0;padding:5px;background:#1abc9c">Student Dashboard</h3>

    <div class="section">
        <h3><i class="fa fa-user" aria-hidden="true"></i>&nbsp;Student</h3>
        <ul style="border-radius:10px;">
            <!-- This button leads to the new profile page instead of direct st_profile -->
            <li><a href="st_profile.php">View Your Profile</a></li>
            <li><a href="view_classes.php">View Classes</a></li>
            <li><a href="view_attendance.php?sid=<?php echo $sid; ?>">Attendance</a></li>
            <li><a href="download_grades.php"><button style="border-radius:10px;transition:all 0.3s ease-in-out;" 
                onmouseover="this.style.backgroundColor='#ddd'" onmouseout="this.style.backgroundColor='#fff'">Download Grades</button></a></li>
        </ul>
    </div>
</div>

<?php include "php/footerbottom.php"; ?>
<?php ob_end_flush(); ?>
