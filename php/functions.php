<?php
class login_registration_class{
	  

   
	public function __construct(){
		$db = new databaseConnection();
	}
	
	//All function for Student
	
	//function for student registration
	public function st_registration($st_id,$st_name,$st_pass,$st_email,$bday,$st_dept,$st_contact,$st_gender,$st_add){
		global $conn;
		$query = $conn->query("select st_id from st_info where st_id='$st_id' or email ='$st_email' ");

		$num = $query->num_rows;
		$in_sql = "INSERT INTO st_info (st_id,name,password,email,bday,program,contact,gender,address) VALUES ('$st_id','$st_name','$st_pass','$st_email','$bday','$st_dept','$st_contact','$st_gender','$st_add') ";
		if($num == 0){
			$conn->query($in_sql);
			return true;
		}else{
			return false;
		}
	}
	
	//function for student login
	public function st_userlogin($st_id, $st_pass){
		global $conn;
		$sql = "SELECT st_id,name FROM st_info WHERE st_id='$st_id' and password='$st_pass'";
		$result = $conn->query($sql);
		$userdata = $result->fetch_assoc();
		$count = $result->num_rows;
		if($count == 1){
			session_start();
			$_SESSION['st_login'] = true; 
			$_SESSION['sid'] = $userdata['st_id']; 
			$_SESSION['sname'] = $userdata['name']; 
			//$_SESSION['login_msg'] = "Login Success"; 
			return true;
		}else{
			return false;
		}
		
	}
	
	//function for get student Name 
	public function getusername($sid){
		global $conn;
		$query = $conn->query("select name from st_info where st_id='$sid'");
		$result = $query->fetch_assoc();
		echo $result['name'];
	}
	// Get all info of a specific student by Student ID
	public function getuserbyid($st_id){
		global $conn;
		$query = $conn->query("select * from st_info where st_id='$st_id'");
		return $query;
	}
	//Update Student Profile
	public function updateprofile($sid,$st_name,$st_email,$st_dept,$st_gender,$st_contact,$st_add,$file){
		global $conn;
		$query = $conn->query("update st_info set name='$st_name',email='$st_email',program='$st_dept',gender='$st_gender',contact='$st_contact', address='$st_add',img='$file' where st_id='$sid'");
		return true;
	}
	
	//Change Student Password
	public function updatePassword($sid, $newpass, $oldpass){
		global $conn;
		$query = $conn->query("select st_id from st_info where st_id='$sid' and password='$oldpass' ");
		$count = $query->num_rows;
		if($count == 0){
			return print("<p style='color:red;text-align:center'>old password not exist.</p>");
		}else{
			$update = $conn->query("update st_info set password='$newpass' where st_id='$sid' ");
			return print("<p style='color:green;text-align:center'>Password changed successfully.</p>");
		}
	}
	//Session Unset for Student info //Log out option
	public function st_logout(){
		$_SESSION['st_login'] = false;
		unset($_SESSION['sid']); 
		unset($_SESSION['sname']);
		unset($_SESSION['st_login']);
		
		//session_destroy();
	}
	public function getsession(){
		return @$_SESSION['st_login'];
	}

	//Ends student releted function 
	
	/**
	---------------------------------
	All functions for faculty section
	---------------------------------
	**/
	public function fct_registration($name,$uname, $pass,$email, $bday,$gender,$edu,$contact,$address){
		global $conn;
		$fct = $conn->query("select id from faculty where username='$uname' ");
		$count = $fct->num_rows;
		if($count == 0){
			$sql = "insert into faculty(name,username,password,email,birthday,gender,education,contact,address) values('$name','$uname','$pass','$email','$bday','$gender','$edu','$contact','$address')";
			$result = $conn->query($sql);
			return true;
		}else{
			return false;
		}
	}
	//get faculty 
	public function get_faculty_by_username($uname){
		global $conn;
		$sql = "select * from faculty where username='$uname'";
		$result = $conn->query($sql);
		return $result;
	}
	public function get_faculty(){
		global $conn;
		$sql = "select * from faculty order by id ASC";
		$result = $conn->query($sql);
		return $result;
	}
	//login for faculty 
	public function fct_login($uname, $pass){
		global $conn;
		$sql = "select id,username,name from faculty where username='$uname' and password='$pass' ";
		$result = $conn->query($sql);
		$count = $result->num_rows;
		$fctinfo = $result->fetch_assoc();
		if($count == 1){
			session_start();
			$_SESSION['fct_login'] = true;
			$_SESSION['f_id'] = $fctinfo['id'];
			$_SESSION['f_uname'] = $fctinfo['username'];
			$_SESSION['f_name'] = $fctinfo['name'];
			return true;
		}else{
			return false;
		}
	}
	public function faculty_logout(){
		$_SESSION['fct_login'] = false;
		unset($_SESSION['f_id']);
		unset($_SESSION['f_uname']);
		unset($_SESSION['f_name']);
		unset($_SESSION['fct_login']);
	}
	public function get_faculty_session(){
		return @$_SESSION['fct_login'];
	}
	
	/*
	**********************
	----------------------
	All functions for Admin 
	----------------------
	**********************
	*/
	
	//for getting All student infomation 
	public function get_all_student(){
		global $conn;
		$sql = "select * from st_info order by st_id ASC";
		$query = $conn->query($sql);
		return $query;
	}
	//search student
	//Search Query
	public function search($query){
		global $conn;
		$result = $conn->query("SELECT * FROM st_info WHERE (st_id LIKE '%".$query."%'
							OR name LIKE '%".$query."%'
								OR contact LIKE '%".$query."%'
									OR email LIKE '%".$query."%') order by st_id");
		return $result;
	}
	
	//Admin log in function 
	public function admin_userlogin($username, $password){
		global $conn;
		$sql  = "select id,username from admin where username='$username' and password='$password'";
		$result = $conn->query($sql);
		$admin_info = $result->fetch_assoc();
		$count = $result->num_rows;
		if($count == 1){
			session_start();
			$_SESSION['admin_login'] = true;
			$_SESSION['admin_id'] = $admin_info['id'];
			$_SESSION['admin_name'] = $admin_info['username'];
			return true;
		}else{
			return false;
		}
		
	}
	public function get_admin_session(){
		return @$_SESSION['admin_login'];
	}
	//admin logout 
	public function admin_logout(){
		$_SESSION['admin_login'] = false;
		unset($_SESSION['admin_id']);
		unset($_SESSION['admin_name']);
		unset($_SESSION['admin_login']);
	}
	//delete student
	public function delete_student($st_id){
		global $conn;
		$sql = "delete from st_info where st_id='$st_id' ";
		$result = $conn->query($sql);
		if($result){
			return true;
		}else{
			return false;
		}
	}
	//attendance system
	
	
public function attn_student() {
    global $conn;
    
    $sql = "SELECT S.STU_ID, P.FNAME 
            FROM student S 
            JOIN person P ON S.STU_ID = P.PERSON_ID";
    $result = $conn->query($sql);
    return $result;
}

	
public function add_attn_student($name, $stid) {
    global $conn;
    $sql = "INSERT INTO student(STU_ID) VALUES('$stid')";
    $result = $conn->query($sql);

    // Optional: Insert into attendance if needed
    $sql2 = "INSERT INTO attendance_details(STU_ID, STATUS) VALUES('$stid', 'P')";
    $result = $conn->query($sql2);

    return $result;
}

	// Insert attendance for students on a given date
	public function insertattn($cur_date, $atten = array()) {
		global $conn;
	
		// Check if attendance already exists for the date
		$sql = "SELECT DISTINCT A.ATTENDANCE_ID 
				FROM attendance A 
				WHERE A.CLASS_DATE = '$cur_date'";  // Assuming you store attendance date in 'CLASS_DATE'
		
		$result = $conn->query($sql);
		while($row = $result->fetch_assoc()) {
			$db_date = $row['ATTENDANCE_ID'];  // Changed to check attendance by ID
			if($cur_date == $db_date){
				return false;  // Attendance already exists
			}
		}
	
		// Loop through attendance data
		foreach ($atten as $key => $attn_value) {
			if ($attn_value == "P") {
				// Insert 'present' attendance in attendance_details
				$sql = "INSERT INTO attendance_details (ATTENDANCE_ID, STU_ID, STATUS) 
						VALUES ((SELECT ATTENDANCE_ID FROM attendance WHERE CLASS_CODE = 'CLASS_CODE' AND CLASS_DATE = '$cur_date'),
						'$key', 'P')";
				$att_res = $conn->query($sql);
			} elseif ($attn_value == "A") {
				// Insert 'absent' attendance in attendance_details
				$sql = "INSERT INTO attendance_details (ATTENDANCE_ID, STU_ID, STATUS) 
						VALUES ((SELECT ATTENDANCE_ID FROM attendance WHERE CLASS_CODE = 'CLASS_CODE' AND CLASS_DATE = '$cur_date'),
						'$key', 'A')";
				$att_res = $conn->query($sql);
			}
		}
	
		if ($att_res) {
			return true;
		} else {
			return false;
		}
	}
	


	// Delete a student from attendance
public function delete_atn_student($at_id) {
    global $conn;

    // Assuming $at_id refers to the student ID (STU_ID)
    $res = $conn->query("DELETE FROM student WHERE STU_ID = '$at_id'");
    return $res;
}

// Get distinct attendance dates
public function get_attn_date() {
    global $conn;

    // Get distinct attendance dates from attendance table
    $res = $conn->query("SELECT DISTINCT CLASS_DATE FROM attendance");
    return $res;
}

// Fetch attendance for all students for a specific date
public function attn_all_student($date) {
    global $conn;

    // Fetch all students and their attendance status for a given date
    $res = $conn->query("SELECT P.FNAME, AD.* 
                         FROM student S
                         JOIN person P ON S.STU_ID = P.PERSON_ID
                         JOIN attendance_details AD ON S.STU_ID = AD.STU_ID
                         JOIN attendance A ON AD.ATTENDANCE_ID = A.ATTENDANCE_ID
                         WHERE A.CLASS_DATE = '$date'");
    return $res;
}

// Update attendance for students on a given date
public function update_attn($date, $atten) {
    global $conn;

    foreach ($atten as $key => $attn_value) {
        // Set attendance status to 'present' or 'absent'
        $status = ($attn_value == "P") ? 'P' : 'A';

        // Update attendance status for the student on the given date
        $sql = "UPDATE attendance_details AD
                JOIN attendance A ON AD.ATTENDANCE_ID = A.ATTENDANCE_ID
                SET AD.STATUS = '$status' 
                WHERE AD.STU_ID = '$key' AND A.class_DATE = '$date'";
        $att_res = $conn->query($sql);
    }

    return $att_res ? true : false;
}


	




	//grading system
	public function add_marks($stid,$subject,$semester,$marks){
		global $conn;
		$qry = "select * from result where st_id='$stid' and sub='$subject' and semester='$semester' ";
		$query = $conn->query($qry);
		$count = $query->num_rows;
		if($count>0){
			return false;
		}else{
		$sql = "insert into result(st_id,marks,sub,semester) values('$stid','$marks','$subject','$semester')";
		$result = $conn->query($sql);
		return $result;
		}
	}
	//show marks
	public function show_marks($stid,$semester){
		global $conn;
		$result = $conn->query("select * from result where st_id='$stid' and semester='$semester' ");
		$count = $result->num_rows;
		if($count>0){
			return $result;
		}else{
			return false;
		}
		
	}
	//update student result
	public function update_result($stid,$subject = array(),$semester){
		global $conn;
		foreach($subject as $key =>$mark ){
			$sql = "update result set marks='$mark' where st_id='$stid' and semester='$semester' and sub='$key' ";
				$result = $conn->query($sql);	
		}
		if($result){
			return true;
		}else{
			return false;
		}
	}
	public function view_cgpa($stid){
		global $conn;
		$sql = "select * from result where st_id='$stid'";
		$result = $conn->query($sql);
		return $result;
	}
	
	
	
	/* Total average marks
	public function sgpa(){
		global $conn;
		$sql = "SELECT avg(marks) as sgpa from result where st_id=12103072 and semester='1st'";
		$result = $conn->query($sql);
		return $result;
	}
	*/
	
	
	
	
	
//end class 	
};



?>