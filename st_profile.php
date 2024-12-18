<?php
session_start();
require "php/config.php";
require_once "php/functions.php";

$user = new login_registration_class();
$sid = $_SESSION['sid']; // This is the student ID
$sname = $_SESSION['sname'];

if(!$user->getsession()){
    header('Location: st_login.php');
    exit();
}
?>	

<?php 
$pageTitle = "Student Profile";
include "php/headertop.php";
?>

<div class="profile">
    <p style="font-size:18px;text-align:center;background:#1abc9c;color:#fff;padding:10px;margin:0">
        Welcome : <?php $user->getusername($sid); ?> <i class="fa fa-check-circle" aria-hidden="true"></i>
    </p>

    <table class="tab_one">
        <?php
        // Get the student information by joining student and person tables
        $getuser = $user->getStudentProfile($sid);
        while($row = $getuser->fetch_assoc()){
        ?>
        <tr>
            <td><b>Student ID:</b> </td>
            <td><?php echo $row['STU_ID']; ?></td>
        </tr>
        <tr>
            <td><b>Name:</b> </td>
            <td><?php echo $row['FNAME'] . " " . $row['LNAME']; ?></td>
        </tr>
        <tr>
            <td><b>E-mail:</b> </td>
            <td><?php echo $row['EMAIL']; ?></td>
        </tr>
        <tr>
            <td><b>Birthday:</b> </td>
            <td><?php echo $row['DOB']; ?></td>
        </tr>
        <tr>
            <td><b>Degree:</b> </td>
            <td><?php echo $row['DEGREE']; ?></td>
        </tr>
        <tr>
            <td><b>Contact:</b> </td>
            <td><?php echo $row['CONTACTNO']; ?></td>
        </tr>
        <tr>
            <td><b>Gender:</b> </td>
            <td><?php echo $row['GENDER']; ?></td>
        </tr>
        <tr>
            <td><b>Address:</b> </td>
            <td>
                <?php 
                echo $row['CITY_CODE'] . ", " . $row['STATE_CODE'] . " - " . $row['POSTAL_CODE']; 
                ?>
            </td>
        </tr>
        
        <!-- <?php if($row['STU_ID'] == $sid){ ?>
        <tr>
            <td><b>Update Profile:</b> </td>
            <td><a href="st_update.php?id=<?php echo $row['STU_ID'];?>"><button class="editbtn">Edit Profile</button></a></td>
        </tr>
        <?php } } ?> -->
    </table>
</div>

<?php include "php/footerbottom.php"; ?>
