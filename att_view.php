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
?>  
<?php 
$pageTitle = "All student details";
include "php/headertop_admin.php";
?>
<div class="all_student fix">
    <h3 style="text-align:center;color:#fff;margin:0;padding:5px;background:#1abc9c">Attendance Management</h3>
    <div class="fix" style="background:#ddd;padding:20px;">
        <span style="float:left;"><button style="background:#58A85D;border:none;color:#fff;padding:10px;"><a style="color:#fff;" href="att_add.php">Add student</a></button></span>
        <span style="float:right;"><button style="background:#58A85D;border:none;color:#fff;padding:10px;"><a style="color:#fff;" href="class_att.php">Take Attendance</a></button></span>
    </div>

    <table class="tab_one" style="text-align:center;">
        <tr>
            <th style="text-align:center;">SL</th>
            <th style="text-align:center;">Attendance Date</th>
            <th style="text-align:center;">Action</th>
        </tr>
        <?php 
        $i = 0;
        // Get attendance dates from the updated table structure
        $get_date = $user->get_attn_date();  // Assumes get_attn_date now returns `CLASS_DATE`
       // $get_date = $user->get_attn_date();
		//var_dump($get_date->fetch_assoc());  // See what data is being returned

        while ($rows = $get_date->fetch_assoc()) {
            $i++;
        ?>
        <tr>
            <td><?php echo $i; ?></td>
            <td><?php echo $rows['CLASS_DATE']; ?></td>  <!-- Corrected column name -->
            <td><a href="att_single_view.php?dt=<?php echo $rows['CLASS_DATE']; ?>">View Attendance</a></td>  <!-- Use 'CLASS_DATE' -->
        </tr>
        <?php } ?>
    </table>
</div>
<?php include "php/footerbottom.php"; ?>
<?php ob_end_flush(); ?>
