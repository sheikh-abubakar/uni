<?php
require 'dompdf/autoload.inc.php'; // Make sure you have autoloaded dompdf

use Dompdf\Dompdf;

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uni"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch attendance shortage list query
$query = "
    SELECT s.STU_ID, 
           p.FNAME,
           cs.CRS_CODE, 
           COUNT(a.STATUS) AS Total_Classes,
           SUM(CASE WHEN a.STATUS = 'P' THEN 1 ELSE 0 END) AS Classes_Attended,
           SUM(CASE WHEN a.STATUS = 'A' THEN 1 ELSE 0 END) AS Classes_Missed,
           ROUND(SUM(CASE WHEN a.STATUS = 'P' THEN 1 ELSE 0 END) / COUNT(a.STATUS) * 100, 2) AS Attendance_Percentage,
           CASE 
               WHEN (SUM(CASE WHEN a.STATUS = 'P' THEN 1 ELSE 0 END) / COUNT(a.STATUS)) * 100 < 75 
               THEN 'Drop due to attendance shortage' 
               ELSE 'Continue'
           END AS Status
    FROM person p 
    JOIN student s ON p.PERSON_ID = s.STU_ID
    JOIN attendance_details a ON s.STU_ID = a.STU_ID 
    JOIN attendance att ON a.ATTENDANCE_ID = att.ATTENDANCE_ID
    JOIN class c ON att.CLASS_CODE = c.CLASS_CODE 
    JOIN course cs ON c.CRS_CODE = cs.CRS_CODE
    GROUP BY s.STU_ID, cs.CRS_CODE
    HAVING Attendance_Percentage < 75
";

$result = $conn->query($query);

// Initialize Dompdf
$dompdf = new Dompdf();

// Prepare the HTML for PDF generation
$html = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Shortage List</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<h1>Attendance Shortage List</h1>
<table>
    <tr>
        <th>Student ID</th>
        <th>Name</th>
        <th>Course Code</th>
        <th>Total Classes</th>
        <th>Classes Attended</th>
        <th>Classes Missed</th>
        <th>Attendance Percentage</th>
        <th>Status</th>
    </tr>';

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $html .= "<tr>
                    <td>{$row['STU_ID']}</td>
                    <td>{$row['FNAME']}</td>
                    <td>{$row['CRS_CODE']}</td>
                    <td>{$row['Total_Classes']}</td>
                    <td>{$row['Classes_Attended']}</td>
                    <td>{$row['Classes_Missed']}</td>
                    <td>{$row['Attendance_Percentage']}</td>
                    <td>{$row['Status']}</td>
                  </tr>";
    }
} else {
    $html .= "<tr><td colspan='8'>No records found.</td></tr>";
}

$html .= '</table>
</body>
</html>';

// Load the HTML content into Dompdf
$dompdf->loadHtml($html);

// Set paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the PDF
$dompdf->render();

// Output the generated PDF (force download)
$dompdf->stream("attendance_shortage_list.pdf", ["Attachment" => true]);

$conn->close();
?>
