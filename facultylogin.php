<?php
ob_start();
session_start();
require "php/config.php";
require_once "php/functions.php";
$user = new login_registration_class();

// Check if faculty session already exists
if ($user->get_faculty_session()) {
    header('Location: professor.php');
    exit();
}

?>

<?php 
$pageTitle = "Professor Login";
include "header.php";
?>

<div class="loginform fix">
    <div class="msg"><h3><i class="fa fa-user" aria-hidden="true"></i> Faculty Login</h3></div>
    <div class="access">
    
        <?php
        // PHP for handling faculty login
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $username = $_POST['user']; // Username corresponds to 'FNAME' in the 'person' table
            $psw = $_POST['psw'];       // Password entered by the user

            if (empty($username) || empty($psw)) {
                echo "<p style='color:red;text-align:center;'>Field must not be empty.</p>";
            } else {
                // Encrypt the password using md5 (You can replace this with a stronger encryption like password_hash())
                
                // Perform faculty login
                $login = $user->fct_login($username, $psw); // Using the fct_login method from functions.php
                
                if ($login) {
                    // Redirect to the professor's dashboard after successful login
                    header('Location: professor.php');
                    exit();
                } else {
                    echo "<p style='color:red;text-align:center'>Incorrect Username or Password</p>";
                }
            }
        }
        ?>
        
        <!-- Faculty Login Form -->
        <form action="" method="post">
            <input type="text" name="user" placeholder="Username" />
            <input type="password" name="psw" placeholder="Password" />
            <input style="color:#ddd;background:#3498db" type="submit" value="Login" />
        </form>
    </div>
</div>

<?php
include "footer.php"; 
ob_end_flush();
?>
