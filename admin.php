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
?>	
<?php 
$pageTitle = "Admin";
include "php/headertop_admin.php";
?>
<div class="admin_profile">
	
	<div class="section">
			<h3><i class="fa fa-graduation-cap" aria-hidden="true"></i>&nbsp;Student</h3>
			<ul style = "border-radius:10px;">
				<li><a href="admin_all_student.php" >View All Students</a></li>
				<li><a href="st_result.php">Student Result</a></li>
				<li><a href="class_att.php">Attendance</a></li>
				<li><a href="add_student.html">Add Student</a></li>
				<li><a href="att_short.php">View attandance shortage list</a></li>
				<li><a href="student_list_pdf.php"><button style="border-radius:10px;transition:all 0.3s ease-in-out;" onmouseover="this.style.backgroundColor='#ddd'" onmouseout="this.style.backgroundColor='#fff'" >Download Student List</button></a></li>
			</ul>
	</div>
	<div class="section">
			<h3><i class="fa fa-male" aria-hidden="true"></i>&nbsp;Professor</h3>
			<ul>
				<li><a href="admin_all_faculty.php">Professor Details</a></li>
				<li><a href="#">Information</a></li>
				<li><a href="#">Search Staff</a></li>
				<li><a href="add_professor.html">Add New Professor</a></li>
				<li><a href="faculty_list.php"><button>Download Staff List</button></a></li>
			</ul>
	</div>
	<!-- <div class="section">
	
			<h3>Registry</h3>
			<ul>
				<li><a href="#">Accounts</a></li>
				<li><a href="#">Salary</a></li>
				<li><a href="#">Student tution fee</a></li>
				<li><a href="#">Other cost</a></li>
			</ul>

	</div> -->

</div>

<?php include "php/footerbottom.php";?>