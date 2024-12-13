<?php
require 'config1.php'; // Database connection
require 'dompdf/autoload.inc.php'; // DOMPDF autoload

use Dompdf\Dompdf;

// Fetch the performance summary data
$sql = "SELECT 
            CRS.CRS_TITLE AS 'Course Name',
            C.CLASS_CODE AS 'Class Code',
            CONCAT(P.FNAME, ' ', P.LNAME) AS 'Professor Name',
            AVG(CASE 
                    WHEN E.ENROLL_GRADE = 'A' THEN 4
                    WHEN E.ENROLL_GRADE = 'B' THEN 3
                    WHEN E.ENROLL_GRADE = 'C' THEN 2
                    WHEN E.ENROLL_GRADE = 'D' THEN 1
                    WHEN E.ENROLL_GRADE = 'F' THEN 0
                    ELSE NULL
                END) AS 'Average Grade',
            (SUM(CASE 
                    WHEN E.ENROLL_GRADE IN ('A', 'B', 'C') THEN 1 
                    ELSE 0 
                END) * 100.0 / COUNT(*)) AS 'Passing Percentage',
            (SUM(CASE 
                    WHEN AD.STATUS = 'P' THEN 1 
                    ELSE 0 
                END) * 100.0 / COUNT(*)) AS 'Attendance Rate'
        FROM 
            ENROLL E
        JOIN 
            CLASS C ON E.CLASS_CODE = C.CLASS_CODE
        JOIN 
            COURSE CRS ON C.CRS_CODE = CRS.CRS_CODE
        JOIN 
            PROFESSOR PR ON C.PROF_ID = PR.PROF_ID
        JOIN 
            PERSON P ON PR.PROF_ID = P.PERSON_ID
        JOIN 
            ATTENDANCE A ON E.CLASS_CODE = A.CLASS_CODE
        JOIN 
            ATTENDANCE_DETAILS AD ON A.ATTENDANCE_ID = AD.ATTENDANCE_ID AND E.STU_ID = AD.STU_ID
        GROUP BY 
            CRS.CRS_TITLE, C.CLASS_CODE, P.FNAME, P.LNAME";

$result = $conn->query($sql);

// Start building the HTML for PDF generation
$html = '<h1>Class Performance Summary</h1>';
$html .= '<table border="1" cellpadding="10" cellspacing="0" width="100%">';
$html .= '<thead><tr>
            <th>Course Name</th>
            <th>Class Code</th>
            <th>Professor Name</th>
            <th>Average Grade</th>
            <th>Passing Percentage</th>
            <th>Attendance Rate</th>
          </tr></thead><tbody>';

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>
                    <td>' . $row['Course Name'] . '</td>
                    <td>' . $row['Class Code'] . '</td>
                    <td>' . $row['Professor Name'] . '</td>
                    <td>' . number_format($row['Average Grade'], 2) . '</td>
                    <td>' . number_format($row['Passing Percentage'], 2) . '%</td>
                    <td>' . number_format($row['Attendance Rate'], 2) . '%</td>
                  </tr>';
    }
} else {
    $html .= '<tr><td colspan="6">No data found for performance summary.</td></tr>';
}

$html .= '</tbody></table>';

// Close database connection
$conn->close();

// Initialize DOMPDF and load the HTML
$dompdf = new Dompdf();
$dompdf->loadHtml($html);

// (Optional) Set paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream("class_performance_summary.pdf", ["Attachment" => 1]);
?>
