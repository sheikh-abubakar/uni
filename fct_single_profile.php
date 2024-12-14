<?php
ob_start();
session_start();
require "php/config.php";
require_once "php/functions.php";
$user = new login_registration_class();
$fid = $_SESSION['f_id'];
$funame = $_SESSION['f_uname'];
$fname = $_SESSION['f_name'];

if (!$user->get_faculty_session()) {
    header('Location: facultylogin.php');
    exit();
}
?>	
<?php 
$pageTitle = "Faculty Profile";
include "php/headertop.php";
?>
<div class="faculty">
    <p style="font-size:18px; text-align:center; background:#1abc9c; color:#fff; padding:10px; margin:0">
        Welcome: <?php echo $funame; ?> <i class="fa fa-check-circle" aria-hidden="true"></i>
    </p>

    <table class="tab_one">
        <?php
        // Fetch faculty data using username
        $getuser = $user->get_faculty_by_username($funame);
        if ($getuser) {
            while ($row = $getuser->fetch_assoc()) {
                ?>
                <tr>
                    <td style="text-align:center">Name:</td>
                    <td><?php echo $row['FNAME'] . " " . $row['LNAME']; ?></td>
                </tr>
                <tr>
                    <td style="text-align:center">Username:</td>
                    <td><?php echo $funame; ?></td>
                </tr>
                <tr>
                    <td style="text-align:center">E-mail:</td>
                    <td><?php echo $row['EMAIL']; ?></td>
                </tr>
                <tr>
                    <td style="text-align:center">Birthday:</td>
                    <td><?php echo $row['DOB']; ?></td>
                </tr>
                <tr>
                    <td style="text-align:center">Education:</td>
                    <td><?php echo $row['PROF_EDUCATION']; ?></td>
                </tr>
                <tr>
                    <td style="text-align:center">Contact:</td>
                    <td><?php echo $row['CONTACTNO']; ?></td>
                </tr>
                <tr>
                    <td style="text-align:center">Gender:</td>
                    <td><?php echo $row['GENDER']; ?></td>
                </tr>
                <tr>
                    <td style="text-align:center">State:</td>
                    <td><?php echo $row['STATE_CODE']; ?></td>
                </tr>
                <tr>
                    <td style="text-align:center">City:</td>
                    <td><?php echo $row['CITY_CODE']; ?></td>
                </tr>
                <tr>
                    <td style="text-align:center">Postal Code:</td>
                    <td><?php echo $row['POSTAL_CODE']; ?></td>
                </tr>
                <?php
            }
        } else {
            echo "<tr><td colspan='2' style='text-align:center'>No data found.</td></tr>";
        }
        ?>
    </table>
</div>

<?php
include "php/footerbottom.php";
ob_end_flush();
?>
