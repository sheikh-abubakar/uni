<?php
// Include the database connection file
include 'config1.php';

// Fetch all students and their results
$sql = "SELECT PERSON.PERSON_ID, PERSON.FNAME, PERSON.LNAME, PERSON.EMAIL, 
               STUDENT.DEGREE, STUDENT.DEPT_CODE, 
               RESULT.SUB, RESULT.MARKS 
        FROM PERSON 
        INNER JOIN STUDENT ON PERSON.PERSON_ID = STUDENT.STU_ID 
        INNER JOIN RESULT ON STUDENT.STU_ID = RESULT.ST_ID";

$result = $conn->query($sql); // Execute the query

// Check if any results were returned
if ($result->num_rows > 0): ?>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
        }
        
        .container {
            width: 80%;
            margin: 40px auto;
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        
        th {
            background-color: #f0f0f0;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
    <div class="container">
    <h1>Student Results</h1>
    <table>
        <thead>
            <tr>
                <th>Person ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Degree</th>
                <th>Department Code</th>
                <th>Subject</th>
                <th>Marks</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['PERSON_ID']; ?></td>
                    <td><?php echo $row['FNAME']; ?></td>
                    <td><?php echo $row['LNAME']; ?></td>
                    <td><?php echo $row['EMAIL']; ?></td>
                    <td><?php echo $row['DEGREE']; ?></td>
                    <td><?php echo $row['DEPT_CODE']; ?></td>
                    <td><?php echo $row['SUB']; ?></td>
                    <td><?php echo $row['MARKS']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    </div>
<?php else: ?>
    <h1>No results found</h1>
<?php endif; ?>

<?php
$conn->close(); // Close the database connection
?>