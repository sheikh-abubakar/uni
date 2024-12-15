<?php
ob_start();
session_start();
require "php/config.php";
require_once "php/functions.php";
$user = new login_registration_class();
$fid = $_SESSION['f_id'];
$funame = $_SESSION['f_uname']; // Username is now FNAME in the `person` table
$fname = $_SESSION['f_name'];

if (!$user->get_faculty_session()) {
    header('Location: facultylogin.php');
    exit();
}

// Fetch professor details using the updated get_faculty_by_username method
$professor_details = $user->get_faculty_by_username($funame); // $funame is the username
$professor_data = $professor_details->fetch_assoc();

if (!$professor_data) {
    echo "Failed to retrieve professor details.";
    exit();
}

// Extract necessary data from the result
$full_name = $professor_data['FNAME'] . ' ' . $professor_data['LNAME'];
$email = $professor_data['EMAIL'];
$dob = $professor_data['DOB'];
$gender = $professor_data['GENDER'];
$education = $professor_data['PROF_EDUCATION'];
$address = $professor_data['STATE_CODE'] . ', ' . $professor_data['CITY_CODE'] . ', ' . $professor_data['POSTAL_CODE'];

?>  
<?php 
$pageTitle = "Professor Profile";
include "php/headertop.php";
?>
<div class="admin_profile">
    <h3 style="text-align:center;color:#fff;margin:0;padding:5px;background:#1abc9c">Professor Profile</h3>

  
    
    <div class="section">
    <h3><i class="fa fa-male" aria-hidden="true"></i>&nbsp;Professor</h3>
        <ul style="border-radius:10px;">
            <li><a href="fct_single_profile.php">View Your Profile</a></li>
            <li><a href="view_class.php">View Classes</a></li>
            <li><a href="view_std_att.php?fid=<?php echo $fid; ?>">Attendance</a></li>
            <li><a href="student_list_pdf.php"><button style="border-radius:10px;transition:all 0.3s ease-in-out;" onmouseover="this.style.backgroundColor='#ddd'" onmouseout="this.style.backgroundColor='#fff'">Download Student List</button></a></li>
        </ul>
    </div>
    
   
</div>

<?php include "php/footerbottom.php"; ?>
<?php ob_end_flush(); ?>
