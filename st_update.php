<?php
session_start();
require "php/config.php";
require_once "php/functions.php";
$user = new login_registration_class();
$sid = $_SESSION['sid'];
$sname = $_SESSION['sname'];

if(!$user->getsession()){
    header('Location: st_login.php');
    exit();
}
?>

<?php 
$pageTitle = "Update Profile";
include "php/headertop.php";
?>

<script>
    function PreviewImage(upname, prv_id) {
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementsByName(upname)[0].files[0]);

        oFReader.onload = function (oFREvent) {
            document.getElementById(prv_id).src = oFREvent.target.result;
        };
    }
</script>

<div class="profile">
    <h3 style="font-size:18px;text-align:center;background:#1abc9c;color:#fff;padding:10px;margin:0">Update Your Profile</h3>
    
    <?php
        $qry = $user->getStudentProfile($sid); // Use the updated function to get profile data
        $row = $qry->fetch_assoc();
        $piclocation = isset($row['img']) ? $row['img'] : 'default.png'; // Ensure there's a fallback if no image is found

        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            // Handle image upload
            function guid() {
                return strtoupper(md5(uniqid(rand(), true)));
            }
            
            if ($_FILES["personal_image"]["name"]) {
                $path_parts = pathinfo($_FILES["personal_image"]["name"]);
                $ext = $path_parts['extension'];
                $fileName = guid() . '.' . $ext;
            } else {
                $fileName = $piclocation;
            }

            move_uploaded_file($_FILES['personal_image']['tmp_name'], "img/student/$fileName");

            // Handle form data
            $st_name = $_POST['st_name'];
            $st_email = $_POST['st_email'];
            $st_degree = $_POST['st_degree'];  // Now using degree instead of program
            $st_contact = $_POST['st_contact'];
            $st_gender = $_POST['st_gender'];
            $st_add = $_POST['st_add'];

            if (empty($st_name) || empty($st_email) || empty($st_contact) || empty($st_degree) || empty($st_gender) || empty($st_add)) {
                echo "<p style='color:red;text-align:center'>Field must not be empty.</p>";
            } else {
                $update = $user->updateProfile($sid, $st_name, $st_email, $st_degree, $st_gender, $st_contact, $st_add, $fileName);
                if ($update) {
                    echo "<h4 style='color:green;text-align:center'>Information Updated successfully</h4>";
                } else {
                    echo "<h4 style='color:red;text-align:center'>Failed to update</h4>";
                }
            }
        }
    ?>
    
    <div class="st_update fix">
        <form action="" method="post" enctype="multipart/form-data">
            <table class="tab_one">
                <tr>
                    <td style="width:250px;"></td>
                    <td>Photo</td>
                    <td>
                        <img id="logo_preview" src="img/student/<?php echo $piclocation; ?>" style="height:150px; width:150px; border:1px green solid;"><br><br> 
                        <input type="file" name="personal_image" id="spic" onchange="PreviewImage('personal_image', 'logo_preview')" />
                    </td>
                </tr>
                <tr>
                    <td style="width:125px;"></td>
                    <td>Name:</td>
                    <td><input type="text" name="st_name" value="<?php echo $row['FNAME'] . ' ' . $row['LNAME']; ?>"></td>
                </tr>
                <tr>
                    <td style="width:125px;"></td>
                    <td>E-mail:</td>
                    <td><input type="email" name="st_email" value="<?php echo $row['EMAIL']; ?>"></td>
                </tr>
                <tr>
                    <td style="width:125px;"></td>
                    <td>Degree:</td> <!-- Program is replaced with Degree -->
                    <td><input type="text" name="st_degree" value="<?php echo $row['DEGREE']; ?>"></td>
                </tr>
                <tr>
                    <td style="width:125px;"></td>
                    <td>Contact:</td>
                    <td><input type="text" name="st_contact" value="<?php echo $row['CONTACTNO']; ?>"></td>
                </tr>
                <tr>
                    <td style="width:125px;"></td>
                    <td>Gender:</td>
                    <td><input type="text" name="st_gender" value="<?php echo $row['GENDER']; ?>"></td>
                </tr>
                <tr>
                    <td style="width:125px;"></td>
                    <td>Address:</td>
                    <td>
                        <input type="text" name="st_add" value="<?php echo $row['CITY_CODE'] . ', ' . $row['STATE_CODE'] . ' - ' . $row['POSTAL_CODE']; ?>">
                    </td>
                </tr>
                <tr>
                    <td style="width:125px;"></td>
                    <td></td>
                    <td colspan="2">
                        <input style="background:#3498db;color:#fff;width:168px;border-radius:5px;" type="submit" name="Update" value="Update">
                    </td>                        
                </tr>
            </table>
        </form>
    </div>
    
    <div class="back fix">
        <p style="text-align:center"><a href="st_profile.php"><button class="editbtn">Back to your Profile</button></a></p>
    </div>
</div>

<?php include "php/footerbottom.php"; ?>
