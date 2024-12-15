<?php
require "config1.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_code = $_GET['class_code']; // Get class code from URL

    // Get all students enrolled in this class
    $student_query = "SELECT STU_ID FROM Enroll WHERE CLASS_CODE = '$class_code'";
    $student_result = $conn->query($student_query);

    if ($student_result->num_rows > 0) {
        // Loop through each student and update their grade
        while ($row = $student_result->fetch_assoc()) {
            $stu_id = $row['STU_ID'];

            // Check if the form submission contains the grade for this student
            if (isset($_POST["grade_$stu_id"])) {
                $new_grade = $_POST["grade_$stu_id"];

                // Update the grade in the database
                $update_query = "UPDATE Enroll SET ENROLL_GRADE = '$new_grade' 
                                 WHERE STU_ID = '$stu_id' AND CLASS_CODE = '$class_code'";
                $conn->query($update_query);

                // Check if the update was successful
                if ($conn->affected_rows > 0) {
                    echo "Grade updated for student ID $stu_id to $new_grade.<br>";
                } else {
                    echo "No change for student ID $stu_id.<br>";
                }
            }
        }
        echo "<a href='view_std_grades.php?class_code=$class_code'>Go back to class grades</a>";
    } else {
        echo "No students found for this class.";
    }
}
?>
