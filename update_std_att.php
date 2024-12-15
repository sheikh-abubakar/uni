<?php
session_start();
include "config1.php"; // Database connection file

if (!isset($_SESSION['f_id'])) {
    header('Location: facultylogin.php');
    exit();
}

$prof_id = $_SESSION['f_id'];

// Fetch classes taught by the professor
$query_classes = "SELECT distinct c.CLASS_CODE, cr.CRS_TITLE 
                  FROM Class c
                  JOIN Course cr ON c.CRS_CODE = cr.CRS_CODE
                  WHERE c.PROF_ID = ?";
$stmt_classes = $conn->prepare($query_classes);
$stmt_classes->bind_param("s", $prof_id);
$stmt_classes->execute();
$result_classes = $stmt_classes->get_result();
$classes = $result_classes->fetch_all(MYSQLI_ASSOC);
$stmt_classes->close();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_code = $_POST['class_code'];
    $class_date = $_POST['class_date'];
    $attendance_data = $_POST['attendance'];

    // Insert or update attendance
    foreach ($attendance_data as $stu_id => $status) {
        $check_query = "SELECT ad.ATTENDANCE_ID FROM ATTENDANCE a
                        JOIN ATTENDANCE_details ad ON a.ATTENDANCE_ID = ad.ATTENDANCE_ID
                        WHERE a.CLASS_CODE = ? AND a.CLASS_DATE = ? AND ad.STU_ID = ?";
        $stmt_check = $conn->prepare($check_query);
        $stmt_check->bind_param("sss", $class_code, $class_date, $stu_id);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $update_query = "UPDATE ATTENDANCE_details ad
                             JOIN ATTENDANCE a ON ad.ATTENDANCE_ID = a.ATTENDANCE_ID
                             SET ad.STATUS = ?
                             WHERE a.CLASS_CODE = ? AND a.CLASS_DATE = ? AND ad.STU_ID = ?";
            $stmt_update = $conn->prepare($update_query);
            $stmt_update->bind_param("ssss", $status, $class_code, $class_date, $stu_id);
            $stmt_update->execute();
            $stmt_update->close();
        } else {
            $insert_query_att = "INSERT INTO ATTENDANCE(CLASS_CODE, CLASS_DATE) VALUES (?, ?)";
            $stmt_insert_att = $conn->prepare($insert_query_att);
            $stmt_insert_att->bind_param("ss", $class_code, $class_date);
            $stmt_insert_att->execute();
            $attendance_id = $conn->insert_id;

            $insert_query_det = "INSERT INTO ATTENDANCE_details (ATTENDANCE_ID, STU_ID, STATUS) VALUES (?, ?, ?)";
            $stmt_insert_det = $conn->prepare($insert_query_det);
            $stmt_insert_det->bind_param("iss", $attendance_id, $stu_id, $status);
            $stmt_insert_det->execute();
            $stmt_insert_det->close();
            $stmt_insert_att->close();
        }
        $stmt_check->close();
    }

    echo "<p class='success'>Attendance updated successfully!</p>";
}

// Fetch students for the selected class
$students = [];
if (isset($_GET['class_code'])) {
    $selected_class = $_GET['class_code'];
    $query_students = "SELECT s.STU_ID, p.FNAME, p.LNAME 
                       FROM Student s 
                       JOIN Person p ON s.STU_ID = p.PERSON_ID
                       WHERE s.STU_ID IN (SELECT STU_ID FROM Enroll WHERE CLASS_CODE = ?)";
    $stmt_students = $conn->prepare($query_students);
    $stmt_students->bind_param("s", $selected_class);
    $stmt_students->execute();
    $result_students = $stmt_students->get_result();
    $students = $result_students->fetch_all(MYSQLI_ASSOC);
    $stmt_students->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Attendance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
        }
        .container {
            width: 90%;
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #444;
        }
        select, input[type="date"], button {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        table th {
            background-color: #007BFF;
            color: white;
            text-transform: uppercase;
        }
        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        button {
            background-color: #007BFF;
            color: #fff;
            cursor: pointer;
            font-size: 1rem;
        }
        button:hover {
            background-color: #0056b3;
        }
        .success {
            text-align: center;
            color: green;
            font-weight: bold;
        }
        .form-section {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Update Attendance</h2>

        <div class="form-section">
            <!-- Class Selection Form -->
            <form method="GET" action="">
                <label for="class_code">Select Class:</label>
                <select name="class_code" id="class_code" required>
                    <option value="">-- Select Class --</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= $class['CLASS_CODE']; ?>"
                            <?= (isset($selected_class) && $selected_class == $class['CLASS_CODE']) ? 'selected' : ''; ?>>
                            <?= $class['CRS_TITLE']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Load Students</button>
            </form>
        </div>

        <?php if (!empty($students)): ?>
            <!-- Attendance Form -->
            <form method="POST" action="">
                <input type="hidden" name="class_code" value="<?= $selected_class; ?>">
                <label for="class_date">Date:</label>
                <input type="date" name="class_date" id="class_date" value="<?= date('Y-m-d'); ?>" required>
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Status (P/A)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?= $student['STU_ID']; ?></td>
                                <td><?= $student['FNAME'] . " " . $student['LNAME']; ?></td>
                                <td>
                                    <select name="attendance[<?= $student['STU_ID']; ?>]" required>
                                        <option value="P" selected>Present</option>
                                        <option value="A">Absent</option>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit">Submit Attendance</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
