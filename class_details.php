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
    header('Location: view_class.php');
    exit();
}

$class_code = $_GET['class_code']; // Get class code from URL
$pageTitle = "Class Details";
include "php/headertop.php";
?>

<div class="class-details-container">
<div class="class-header">
    <h2>Class Details for Class Code: <?php echo $class_code; ?></h2>
    <a href="view_std_grades.php?class_code=<?php echo $class_code; ?>">
        <button class="grade-btn">View Class Grades</button>
    </a>
</div>


    <div class="class-info">
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
    </div>

    <h3>List of Enrolled Students:</h3>
    <table class="students-table">
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Enrollment Date</th>
            </tr>
        </thead>
        <tbody>
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
        </tbody>
    </table>
</div>

<?php include "php/footerbottom.php"; ?>

<!-- Add the custom styles in a separate style file or within the page -->
<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f7f7f7;
        margin: 0;
        padding: 0;
    }
    .class-details-container {
        width: 90%;
        margin: 30px auto;
        background-color: #fff;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }
    .class-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    h2 {
        font-size: 24px;
        color: #333;
    }
    .grade-btn {
        background-color: #0073e6;
        color: white;
        border: none;
        padding: 10px 15px;
        font-size: 14px;
        border-radius: 5px;
        cursor: pointer;
    }
    .grade-btn:hover {
        background-color: #005bb5;
    }
    .class-info p {
        font-size: 16px;
        margin: 5px 0;
        color: #444;
    }
    .students-table {
        width: 100%;
        margin-top: 20px;
        border-collapse: collapse;
    }
    .students-table th, .students-table td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
    }
    .students-table th {
        background-color: #0073e6;
        color: white;
        font-weight: bold;
    }
    .students-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .students-table tr:hover {
        background-color: #f1f1f1;
    }
    .students-table td {
        color: #333;
    }
</style>
