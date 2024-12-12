<?php
include 'config1.php'; // Include the database connection

// Fetch all professors
$sql = "SELECT PERSON.PERSON_ID, PERSON.FNAME, PERSON.LNAME, PERSON.EMAIL, PERSON.DOB, PERSON.GENDER, PERSON.CONTACTNO, PERSON.STATE_CODE, PERSON.CITY_CODE, PERSON.POSTAL_CODE, PROFESSOR.PROF_EDUCATION, PROFESSOR.PROF_SPECIALITY, PROFESSOR.DEPT_CODE 
        FROM PERSON
        INNER JOIN PROFESSOR ON PERSON.PERSON_ID = PROFESSOR.PROF_ID";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Professors</title>
    <link rel="stylesheet" href="css/std.css">
</head>
<body>
    <div class="table-container">
        <h1>Professor List</h1>
        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>Person ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Date of Birth</th>
                <th>Gender</th>
                <th>Contact No</th>
                <th>State Code</th>
                <th>City Code</th>
                <th>Postal Code</th>
                <th>Education</th>
                <th>Speciality</th>
                <th>Department Code</th>
            </tr>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['PERSON_ID']; ?></td>
                    <td><?php echo $row['FNAME']; ?></td>
                    <td><?php echo $row['LNAME']; ?></td>
                    <td><?php echo $row['EMAIL']; ?></td>
                    <td><?php echo $row['DOB']; ?></td>
                    <td><?php echo $row['GENDER']; ?></td>
                    <td><?php echo $row['CONTACTNO']; ?></td>
                    <td><?php echo $row['STATE_CODE']; ?></td>
                    <td><?php echo $row['CITY_CODE']; ?></td>
                    <td><?php echo $row['POSTAL_CODE']; ?></td>
                    <td><?php echo $row['PROF_EDUCATION']; ?></td>
                    <td><?php echo $row['PROF_SPECIALITY']; ?></td>
                    <td><?php echo $row['DEPT_CODE']; ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="13">No professors found</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
