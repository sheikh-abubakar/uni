<?php
session_start();
require "php/config.php";
require_once "php/functions.php";
$user = new login_registration_class();
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'];
if(!$user->get_admin_session()){
    header('Location: index.php');
    exit();
}

if(isset($_REQUEST['dt'])){
    $date = $_REQUEST['dt'];
}
?>  
<?php 
$pageTitle = "All student details";
include "php/headertop_admin.php";
?>
<div class="all_student fix">
    <h3 style="text-align:center;color:#fff;margin:0;padding:5px;background:#1abc9c">Attendance Management</h3>
    <div class="fix" style="background:#ddd;padding:20px;">
        <span style="float:left;"><a style="color:#fff;" href="class_att.php"><button style="background:#58A85D;border:none;color:#fff;padding:10px;">Take Attendance</button></a></span>
        <span style="float:right;"><a style="color:#fff;" href="att_view.php"><button style="background:#58A85D;border:none;color:#fff;padding:10px;">View Attendance</button></a></span>
    </div>
    <p style="text-align:center;color:#34495e;margin:0;padding-top:8px;color:red;font-size:22px;">
        <?php echo "Attendance of: ".$date;?>
    </p>
    <?php
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $atten = $_POST['attn'];
        $res = $user->update_attn($date, $atten);
        if($res){
            echo "<h3 style='color:green;margin:0;padding:0;text-align:center'>Attendance Updated successfully!</h3>";
        }else{
            echo  "<p style='color:red;text-align:center'>Failed to update data</p>";
        }
    }
    ?>
    
    <form action="" method="post">
        <table class="tab_one" style="text-align:center;">
            <tr>
                <th style="text-align:center;">SL</th>
                <th style="text-align:center;">Name</th>
                <th style="text-align:center;">ID</th>
                <th style="text-align:center;">Attendance</th>
            </tr>
            <?php 
            $i = 0;
            // Fetch attendance details for the given date
            $std = $user->attn_all_student($date);
            
            if($std){
                while($rows = $std->fetch_assoc()){
                    $i++;
                    $full_name = $rows['FNAME'] ; // Concatenate first and last names
            ?>
            <tr>
                <td><?php echo $i;?></td>
                <td><?php echo $full_name; // Show student's full name ?></td>
                <td><?php echo $rows['STU_ID']; // Show student ID ?></td>
                <td>
                    <label style="color:red;font-size:20px"><input type="radio" name="attn[<?php echo $rows['STU_ID'];?>]" value="absent" <?php if($rows['STATUS'] == "A") echo "checked";?>/>Absent</label>
                    <label style="color:green;font-size:20px"> <input type="radio" name="attn[<?php echo $rows['STU_ID'];?>]" value="present" <?php if($rows['STATUS'] == "P") echo "checked";?>/>Present</label>
                </td>
            </tr>
            <?php 
                }
            } else {
                echo "<tr><td colspan='4'>No attendance records found for this date.</td></tr>";
            }
            ?>
        </table>
        
        <center>
            <span><input style="background:#58A85D;border:none;color:#fff;padding:8px 100px;" type="submit" name="submit" value="Update" /></span>
        </center>
    </form>
</div>

<?php include "php/footerbottom.php"; ?>
<?php ob_end_flush(); ?>
