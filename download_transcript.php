<?php
session_start();
require "php/config.php"; // Database connection file
require_once "php/functions.php"; // Functions for student results
require 'dompdf/autoload.inc.php'; // Include the Dompdf library

use Dompdf\Dompdf;

// Initialize the Dompdf class
$dompdf = new Dompdf();

// Check if Student ID is set
if (!isset($_GET['sid'])) {
    die("Student ID not provided!");
}

$user = new login_registration_class();
$stid = $_GET['sid'];

// Fetch student information
$get_result = $user->view_cgpa($stid);

if (!$get_result || $get_result->num_rows == 0) {
    die("No data found for the given student ID.");
}

// Fetch student name
$student_name = "Unknown";
if (isset($_SESSION['sname'])) {
    $student_name = $_SESSION['sname'];
}

// Initialize variables for calculations
$total_courses = 0;
$total_credit_hours = 0;
$total_grade_points = 0;

// HTML content for PDF generation
$html = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        h2 { text-align: center; color: #4CAF50; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ddd; text-align: center; }
        th, td { padding: 10px; }
        th { background-color: #f2f2f2; }
        .total { font-weight: bold; }
    </style>
</head>
<body>
    <h2>Student Transcript</h2>
    <p><strong>Name:</strong> {$student_name}</p>
    <p><strong>Student ID:</strong> {$stid}</p>

    <table>
        <tr>
            <th>Subject</th>
            <th>Marks</th>
            <th>Grade</th>
            <th>Credit Hours</th>
            <th>Status</th>
        </tr>";

// Loop through results and generate table rows
while ($row = $get_result->fetch_assoc()) {
    $subject = $row['sub'];
    $marks = $row['marks'];

    // Calculate Grade and Status
    $credit_hours = credit_hour($subject);
    $grade_point = grade_point($marks);
    $total_credit_hours += $credit_hours;
    $total_grade_points += $credit_hours * $grade_point;

    $grade = get_grade($marks);
    $status = ($marks < 60) ? "Fail" : (($marks < 70) ? "Retake" : "Pass");

    $html .= "
        <tr>
            <td>{$subject}</td>
            <td>{$marks}</td>
            <td>{$grade}</td>
            <td>{$credit_hours}</td>
            <td>{$status}</td>
        </tr>";
    $total_courses++;
}

// Calculate CGPA
$cgpa = $total_grade_points / $total_credit_hours;
$final_status = ($cgpa >= 3.5) ? "Excellent" : (($cgpa >= 3.0) ? "Good" : (($cgpa >= 2.5) ? "Average" : "Probation"));

$html .= "
        <tr class='total'>
            <td>Total Courses</td>
            <td colspan='2'>{$total_courses}</td>
            <td>{$total_credit_hours}</td>
            <td></td>
        </tr>
        <tr class='total'>
            <td colspan='2'>CGPA</td>
            <td colspan='3'>" . round($cgpa, 2) . " ({$final_status})</td>
        </tr>
    </table>
</body>
</html>";

// Load the HTML content into Dompdf
$dompdf->loadHtml($html);

// Set paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the PDF (generate)
$dompdf->render();

// Stream the PDF to the browser for download
$filename = "transcript_{$stid}.pdf";
$dompdf->stream($filename, ["Attachment" => 1]);

// Custom functions for credit hours, grade point, and grade
function credit_hour($subject) {
    switch ($subject) {
        case "DBMS": return 3;
        case "DBMS Lab": return 1;
        case "Mathematics": return 4;
        case "Programming": return 3;
        case "Programming Lab": return 1;
        case "English": return 4;
        case "Physics": return 3;
        case "Chemistry": return 3;
        case "Psychology": return 3;
        default: return 0;
    }
}

function grade_point($marks) {
    if ($marks < 60) return 0;
    elseif ($marks < 70) return 1;
    elseif ($marks < 80) return 2;
    elseif ($marks < 90) return 3;
    else return 4;
}

function get_grade($marks) {
    if ($marks < 60) return "F";
    elseif ($marks < 70) return "D";
    elseif ($marks < 80) return "C";
    elseif ($marks < 90) return "B";
    else return "A";
}
?>
