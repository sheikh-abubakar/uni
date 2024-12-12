<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Shortage List</title>
    <!-- Add custom CSS here -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            padding: 20px;
            background-color: #0073e6;
            color: white;
            margin: 0;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            text-align: left;
        }

        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #0073e6;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        .status-drop {
            color: red;
            font-weight: bold;
        }

        .status-continue {
            color: green;
            font-weight: bold;
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #0073e6;
            color: white;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
    </style>
</head>
<body>
    <h1>Attendance Shortage List</h1>
    <div class="container">
        <?php
        // Database connection (Adjust your own DB details)
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "uni";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Your updated SQL query
        $sql = "
            SELECT s.STU_ID, 
                   p.FNAME AS Student_Name, 
                   cs.CRS_CODE AS Course_Code,
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
            HAVING Attendance_Percentage < 75";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Course Code</th>
                        <th>Total Classes</th>
                        <th>Classes Attended</th>
                        <th>Classes Missed</th>
                        <th>Attendance Percentage</th>
                        <th>Status</th>
                    </tr>";
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                $statusClass = $row['Status'] === 'Drop due to attendance shortage' ? 'status-drop' : 'status-continue';
                echo "<tr>
                        <td>{$row['STU_ID']}</td>
                        <td>{$row['Student_Name']}</td>
                        <td>{$row['Course_Code']}</td>
                        <td>{$row['Total_Classes']}</td>
                        <td>{$row['Classes_Attended']}</td>
                        <td>{$row['Classes_Missed']}</td>
                        <td>{$row['Attendance_Percentage']}%</td>
                        <td class='{$statusClass}'>{$row['Status']}</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No students have attendance shortage.</p>";
        }

        $conn->close();
        ?>
    </div>

    <footer>
        &copy; <?php echo date("Y"); ?> University Admin
    </footer>
</body>
</html>
