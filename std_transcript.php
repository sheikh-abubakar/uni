<?php
session_start();
require "php/config.php";
require_once "php/functions.php";
$user = new login_registration_class();
$sid = $_SESSION['sid'];
$sname = $_SESSION['sname'];

if (!$user->getsession()) {
    header('Location: st_login.php');
    exit();
}

// Fetch student details
$student_query = "SELECT p.PERSON_ID, p.FNAME, p.LNAME, p.EMAIL, p.DOB, p.GENDER, p.CONTACTNO, p.STATE_CODE, p.CITY_CODE, p.POSTAL_CODE, s.DEGREE 
                  FROM PERSON p 
                  JOIN STUDENT s ON p.PERSON_ID = s.STU_ID 
                  WHERE s.STU_ID = '$sid'";
$student_result = $conn->query($student_query);
$student_data = $student_result->fetch_assoc();

// Fetch course and CGPA details
$course_query = "SELECT c.CRS_CODE, co.CRS_TITLE, co.CRS_CREDITS, e.ENROLL_GRADE 
                 FROM ENROLL e 
                 JOIN CLASS c ON e.CLASS_CODE = c.CLASS_CODE
                 JOIN COURSE co ON c.CRS_CODE = co.CRS_CODE
                 WHERE e.STU_ID = '$sid'";
$course_result = $conn->query($course_query);

function calculate_gpa($course_result) {
    $total_credits = 0;
    $total_points = 0;
    while ($course = $course_result->fetch_assoc()) {
        $grade = $course['ENROLL_GRADE'];
        $credits = $course['CRS_CREDITS'];
        
        // Convert grade to grade points (assuming A=4, B=3, C=2, D=1, F=0)
        switch ($grade) {
            case 'A': $points = 4; break;
            case 'B': $points = 3; break;
            case 'C': $points = 2; break;
            case 'D': $points = 1; break;
            case 'F': $points = 0; break;
            default: $points = 0;
        }
        $total_points += $points * $credits;
        $total_credits += $credits;
    }
    return ($total_credits > 0) ? round($total_points / $total_credits, 2) : 0;
}

$gpa = calculate_gpa($conn->query($course_query)); // Call again as the result set is consumed
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Transcript</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }
        .transcript-container {
            border: 1px solid #000;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 24px;
        }
        .student-details, .course-details {
            width: 100%;
            margin-bottom: 20px;
        }
        .student-details th, .course-details th, .course-details td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .student-details th {
            background-color: #f4f4f4;
        }
        .course-details th {
            background-color: #0073e6;
            color: white;
        }
        .course-details {
            border-collapse: collapse;
            width: 100%;
        }
        .gpa-info {
            margin-top: 20px;
            font-size: 18px;
        }
        .download-btn {
            display: block;
            width: 200px;
            padding: 10px;
            background-color: #0073e6;
            color: white;
            text-align: center;
            margin: 20px auto;
            border: none;
            cursor: pointer;
        }
        .download-btn:hover {
            background-color: #005bb5;
        }
    </style>
</head>
<body>

<div class="transcript-container">
    <div class="header">
        <h2>Student Transcript</h2>
    </div>

    <table class="student-details">
        <tr>
            <th>Student Name:</th>
            <td><?php echo $student_data['FNAME'] . " " . $student_data['LNAME']; ?></td>
        </tr>
        <tr>
            <th>Student ID:</th>
            <td><?php echo $student_data['PERSON_ID']; ?></td>
        </tr>
        <tr>
            <th>Email:</th>
            <td><?php echo $student_data['EMAIL']; ?></td>
        </tr>
        <tr>
            <th>Date of Birth:</th>
            <td><?php echo $student_data['DOB']; ?></td>
        </tr>
        <tr>
            <th>Degree:</th>
            <td><?php echo $student_data['DEGREE']; ?></td>
        </tr>
        <tr>
            <th>Address:</th>
            <td><?php echo $student_data['CITY_CODE'] . ", " . $student_data['STATE_CODE'] . ", " . $student_data['POSTAL_CODE']; ?></td>
        </tr>
    </table>

    <h3>Courses Completed:</h3>
    <table class="course-details">
        <thead>
            <tr>
                <th>Course Code</th>
                <th>Course Title</th>
                <th>Credits</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($course = $course_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $course['CRS_CODE']; ?></td>
                    <td><?php echo $course['CRS_TITLE']; ?></td>
                    <td><?php echo $course['CRS_CREDITS']; ?></td>
                    <td><?php echo $course['ENROLL_GRADE']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="gpa-info">
        <p><strong>Total GPA:</strong> <?php echo $gpa; ?></p>
    </div>

    <button class="download-btn" onclick="window.location.href='download_transcript.php'">Download Transcript</button>
</div>

</body>
</html>

<?php $conn->close(); ?>
