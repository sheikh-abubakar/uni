<?php
session_start();
require "php/config.php";
require_once "php/functions.php";
$user = new login_registration_class();
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'];

if (!$user->get_admin_session()) {
    header('Location: index.php');
    exit();
}

$attendance_threshold = 75;  // Set the threshold for low attendance
$students = $user->get_students_with_low_attendance($attendance_threshold);
?>

<?php 
$pageTitle = "Students with Low Attendance";
include "php/headertop_admin.php";
?>

<div class="all_student fix">
    <h2 style="text-align:center;color:#fff;background:#1abc9c;padding:10px;">Students with Attendance Below <?php echo $attendance_threshold; ?>%</h2>
    
    <table class="tab_one" style="text-align:center; width:100%;">
        <tr>
            <th style="text-align:center;">SL</th>
            <th style="text-align:center;">Name</th>
            <th style="text-align:center;">Student ID</th>
            <th style="text-align:center;">Present Days</th>
            <th style="text-align:center;">Total Classes</th>
            <th style="text-align:center;">Attendance (%)</th>
        </tr>
        <?php 
        $i = 0;
        while($row = $students->fetch_assoc()) {
            $i++;
            $attendance_percentage = round($row['attendance_percentage'], 2);  // Round to 2 decimal places
        ?>
        <tr>
            <td><?php echo $i; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['st_id']; ?></td>
            <td><?php echo $row['present_count']; ?></td>
            <td><?php echo $row['total_classes']; ?></td>
            <td><?php echo $attendance_percentage; ?>%</td>
        </tr>
        <?php } ?>
    </table>
</div>

<?php include "php/footerbottom.php"; ?>
