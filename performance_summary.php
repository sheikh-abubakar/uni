<?php
// Include the database connection and DOMPDF
require 'config1.php'; // Database connection
require 'dompdf/autoload.inc.php'; // DOMPDF autoload

use Dompdf\Dompdf;

// Fetch the performance summary report from the database
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

// Start the HTML for displaying the performance summary
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Performance Summary</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .button-container {
            text-align: center;
            margin-top: 20px;
        }
        .download-button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .download-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>Class Performance Summary</h1>

    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Class Code</th>
                    <th>Professor Name</th>
                    <th>Average Grade</th>
                    <th>Passing Percentage</th>
                    <th>Attendance Rate</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['Course Name']; ?></td>
                        <td><?php echo $row['Class Code']; ?></td>
                        <td><?php echo $row['Professor Name']; ?></td>
                        <td><?php echo number_format($row['Average Grade'], 2); ?></td>
                        <td><?php echo number_format($row['Passing Percentage'], 2); ?>%</td>
                        <td><?php echo number_format($row['Attendance Rate'], 2); ?>%</td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No data found for performance summary.</p>
    <?php endif; ?>

    <!-- Button for downloading the report as a PDF -->
    <div class="button-container">
        <form action="download_summary.php" method="post">
            <button type="submit" class="download-button">Download PDF</button>
        </form>
    </div>
</body>
</html>

<?php $conn->close(); ?>

