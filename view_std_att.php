<?php
session_start();
include "config1.php"; // Database connection file

// Check if professor is logged in
if (!isset($_SESSION['f_id'])) {
    header('Location: facultylogin.php');
    exit();
}

$prof_id = $_SESSION['f_id']; // Logged-in professor's ID
$selected_class = isset($_GET['class_code']) ? $_GET['class_code'] : null;
$date_filter = isset($_GET['att_date']) ? $_GET['att_date'] : date('Y-m-d');

// Fetch classes taught by the professor
$query_classes = "SELECT distinct c.CLASS_CODE, cr.CRS_TITLE, c.ROOM_CODE
                  FROM Class c
                  JOIN Course cr ON c.CRS_CODE = cr.CRS_CODE
                  WHERE c.PROF_ID = ?";
$stmt_classes = $conn->prepare($query_classes);
$stmt_classes->bind_param("s", $prof_id);
$stmt_classes->execute();
$result_classes = $stmt_classes->get_result();
$classes = $result_classes->fetch_all(MYSQLI_ASSOC);
$stmt_classes->close();

// Fetch attendance if a class is selected
$attendance_data = [];
if ($selected_class) {
    $query_attendance = "SELECT a.ATTENDANCE_ID, a.CLASS_DATE, a.START_TIME, a.END_TIME,
                                ad.STU_ID, ad.STATUS, p.FNAME, p.LNAME
                         FROM Attendance a
                         JOIN Attendance_details ad ON a.ATTENDANCE_ID = ad.ATTENDANCE_ID
                         JOIN Student s ON ad.STU_ID = s.STU_ID
                         JOIN Person p ON s.STU_ID = p.PERSON_ID
                         WHERE a.CLASS_CODE = ? AND a.CLASS_DATE = ?";
    $stmt_attendance = $conn->prepare($query_attendance);
    $stmt_attendance->bind_param("ss", $selected_class, $date_filter);
    $stmt_attendance->execute();
    $result_attendance = $stmt_attendance->get_result();
    $attendance_data = $result_attendance->fetch_all(MYSQLI_ASSOC);
    $stmt_attendance->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance</title>

    <!-- Flatpickr CSS for Calendar -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Custom CSS -->
    <style>
        /* Body Styling */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        /* Main Heading */
        h2 {
            text-align: center;
            color: #333;
            margin-top: 30px;
            font-size: 28px;
            font-weight: 600;
        }

        /* Form Styling */
        form {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            max-width: 1000px;
            width: 90%;
        }

        label {
            font-size: 16px;
            color: #444;
            font-weight: 500;
        }

        select, input, button {
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ccc;
            outline: none;
            transition: all 0.3s ease;
            width: 200px;
        }

        select:focus, input:focus, button:focus {
            border-color: #007bff;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Table Styling */
        table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
            background-color: #ffffff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        a.edit-btn {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        a.edit-btn:hover {
            text-decoration: underline;
        }

        /* No Attendance Message */
        p {
            text-align: center;
            color: #ff0000;
        }

        /* Take Attendance Link Styling */
        .take-attendance {
            display: block;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
            color: #007bff;
        }

        .take-attendance:hover {
            text-decoration: underline;
        }

        /* Responsiveness */
        @media (max-width: 768px) {
            form {
                flex-direction: column;
                align-items: stretch;
                width: 100%;
            }

            label, select, input, button {
                width: 100%;
                margin-bottom: 15px;
            }

            h3 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <h2>View Attendance</h2>

    <!-- Form to Select Class and Date -->
    <form method="GET" action="view_std_att.php">
        <div>
            <label for="class_code">Select Class:</label>
            <select name="class_code" id="class_code" required>
                <option value="">-- Select Class --</option>
                <?php foreach ($classes as $class): ?>
                    <option value="<?= htmlspecialchars($class['CLASS_CODE']); ?>" 
                        <?= ($class['CLASS_CODE'] == $selected_class) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($class['CRS_TITLE']) . " (" . htmlspecialchars($class['ROOM_CODE']) . ")"; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="att_date">Select Date:</label>
            <input type="text" name="att_date" id="att_date" value="<?= htmlspecialchars($date_filter); ?>" required>
        </div>

        <div>
            <button type="submit">View Attendance</button>
        </div>
    </form>

    <!-- Display Attendance Table -->
    <?php if ($selected_class): ?>
        <h3 style="text-align: center;">
            Attendance for Class: <?= htmlspecialchars($selected_class); ?> on <?= htmlspecialchars($date_filter); ?>
        </h3>

        <div class="take-attendance">
            <a href="update_std_att.php">Take Attendance</a>
        </div>

        <?php if (!empty($attendance_data)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Status (P/A)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance_data as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['STU_ID']); ?></td>
                            <td><?= htmlspecialchars($row['FNAME'] . " " . $row['LNAME']); ?></td>
                            <td><?= htmlspecialchars($row['STATUS']); ?></td>
                            <td>
                                <a class="edit-btn" href="update_std_att.php?attendance_id=<?= htmlspecialchars($row['ATTENDANCE_ID']); ?>&stu_id=<?= htmlspecialchars($row['STU_ID']); ?>&date=<?= htmlspecialchars($row['CLASS_DATE']); ?>">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No attendance records found for this date.</p>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Flatpickr JS for Calendar -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#att_date", {
            dateFormat: "Y-m-d",
            defaultDate: "<?= $date_filter; ?>",
            altInput: true,
            altFormat: "F j, Y",
            allowInput: true
        });
    </script>
</body>
</html>
