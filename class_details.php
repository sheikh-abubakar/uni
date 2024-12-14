<?php
session_start();
require "php/config.php";
require_once "php/functions.php";

$user = new login_registration_class();
$fid = $_SESSION['f_id']; // Get the professor ID from session
$funame = $_SESSION['f_uname'];

if (!$user->get_faculty_session()) {
    header('Location: facultylogin.php');
    exit();
}

if (!isset($_GET['class_code'])) {
    header('Location: view_classes.php');
    exit();
}

$class_code = $_GET['class_code']; // Get class code from URL
$pageTitle = "Class Details";
include "php/headertop.php";
?>

<div class="class_details">
    <h3>Class Details for Class Code: <?php echo $class_code; ?></h3>

    <?php
    // Get course and class details
    $query = "SELECT co.CRS_TITLE, co.CRS_DESCRIPTION, co.CRS_CREDITS, c.ROOM_CODE 
              FROM Class c
              JOIN Course co ON c.CRS_CODE = co.CRS_CODE
              WHERE c.CLASS_CODE = '$class_code' AND c.PROF_ID = '$fid'";
    $result = $conn->query($query);
    $class_data = $result->fetch_assoc();

    if ($class_data) {
        echo "<p><strong>Course Title:</strong> {$class_data['CRS_TITLE']}</p>";
        echo "<p><strong>Course Description:</strong> {$class_data['CRS_DESCRIPTION']}</p>";
        echo "<p><strong>Credits:</strong> {$class_data['CRS_CREDITS']}</p>";
        echo "<p><strong>Room Code:</strong> {$class_data['ROOM_CODE']}</p>";
    } else {
        echo "<p>Class details not found.</p>";
    }

    // Get the number of students enrolled in this class
    $enroll_query = "SELECT COUNT(*) as total_students FROM Enroll WHERE CLASS_CODE = '$class_code'";
    $enroll_result = $conn->query($enroll_query);
    $enroll_data = $enroll_result->fetch_assoc();

    echo "<p><strong>Total Students Enrolled:</strong> {$enroll_data['total_students']}</p>";
    ?>

    <h4>List of Enrolled Students:</h4>
    <table class="tab_one">
        <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Enrollment Date</th>
        </tr>

        <?php
        // Get the details of students enrolled in this class
        $student_query = "SELECT e.STU_ID, p.FNAME, p.LNAME, p.EMAIL, e.ENROLL_DATE
                          FROM Enroll e
                          JOIN Student s ON e.STU_ID = s.STU_ID
                          JOIN Person p ON s.STU_ID = p.PERSON_ID
                          WHERE e.CLASS_CODE = '$class_code'";
        $student_result = $conn->query($student_query);

        if ($student_result->num_rows > 0) {
            while ($row = $student_result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['STU_ID']}</td>
                        <td>{$row['FNAME']} {$row['LNAME']}</td>
                        <td>{$row['EMAIL']}</td>
                        <td>{$row['ENROLL_DATE']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No students enrolled in this class yet.</td></tr>";
        }
        ?>
    </table>
</div>

<?php include "php/footerbottom.php"; ?>
