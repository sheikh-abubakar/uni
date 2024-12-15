<?php
session_start();
require "config1.php";

// Ensure that the professor is logged in
if (!isset($_SESSION['f_id'])) {
    header('Location: facultylogin.php');
    exit();
}

$fid = $_SESSION['f_id'];
$class_code = $_GET['class_code']; // Get class code from URL

// Get class and course details
$query = "SELECT co.CRS_TITLE, co.CRS_DESCRIPTION 
          FROM Class c 
          JOIN Course co ON c.CRS_CODE = co.CRS_CODE 
          WHERE c.CLASS_CODE = '$class_code' AND c.PROF_ID = '$fid'";
$result = $conn->query($query);
$class_data = $result->fetch_assoc();

if (!$class_data) {
    echo "Class details not found.";
    exit();
}

// Get student details and grades for this class
$student_query = "SELECT e.STU_ID, p.FNAME, p.LNAME, p.EMAIL, e.ENROLL_DATE, e.ENROLL_GRADE
                  FROM Enroll e
                  JOIN Student s ON e.STU_ID = s.STU_ID
                  JOIN Person p ON s.STU_ID = p.PERSON_ID
                  WHERE e.CLASS_CODE = '$class_code'";
$student_result = $conn->query($student_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Class Grades</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f9; }
        .container { width: 80%; margin: 50px auto; background-color: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        h2 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        th { background-color: #0073e6; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        input[type="text"] { width: 100%; padding: 5px; }
        .submit-btn { background-color: #0073e6; color: white; padding: 10px 20px; border: none; cursor: pointer; margin-top: 20px; }
        .submit-btn:hover { background-color: #005bb5; }
    </style>
</head>
<body>

<div class="container">
    <h2>Grades for Class: <?php echo $class_code; ?></h2>
    <p><strong>Course Title:</strong> <?php echo $class_data['CRS_TITLE']; ?></p>
    <p><strong>Course Description:</strong> <?php echo $class_data['CRS_DESCRIPTION']; ?></p>

    <form action="update_std_grades.php?class_code=<?php echo $class_code; ?>" method="POST">
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Enrollment Date</th>
                    <th>Grade</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if ($student_result->num_rows > 0) {
                while ($row = $student_result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['STU_ID']}</td>
                            <td>{$row['FNAME']} {$row['LNAME']}</td>
                            <td>{$row['EMAIL']}</td>
                            <td>{$row['ENROLL_DATE']}</td>
                            <td><input type='text' name='grade_{$row['STU_ID']}' value='{$row['ENROLL_GRADE']}' /></td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No students enrolled in this class.</td></tr>";
            }
            ?>
            </tbody>
        </table>
        <input type="submit" class="submit-btn" value="Update Grades">
    </form>
</div>

</body>
</html>
