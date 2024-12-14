<?php
session_start();
require "php/config.php";
require_once "php/functions.php";

$user = new login_registration_class();
$fid = $_SESSION['f_id']; // Get the professor ID from session
$funame = $_SESSION['f_uname'];

if (!$user->get_faculty_session()) {
    header('Location: facultylogin.php');
    exit();
}
$pageTitle = "View Classes";
include "php/headertop.php";
?>

<div class="classes_list">
    <h3>Your Classes</h3>
    <table class="tab_one">
        <tr>
            <th>Class Code</th>
            <th>Course Title</th>
            <th>Course Credits</th>
            <th>Room Code</th>
            <th>Action</th>
        </tr>

        <?php
        // Get the classes assigned to the professor
        $query = "SELECT c.CLASS_CODE, co.CRS_TITLE, co.CRS_CREDITS, c.ROOM_CODE 
                  FROM Class c
                  JOIN Course co ON c.CRS_CODE = co.CRS_CODE
                  WHERE c.PROF_ID = '$fid'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['CLASS_CODE']}</td>
                        <td>{$row['CRS_TITLE']}</td>
                        <td>{$row['CRS_CREDITS']}</td>
                        <td>{$row['ROOM_CODE']}</td>
                        <td><a href='class_details.php?class_code={$row['CLASS_CODE']}'>View Details</a></td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No classes found</td></tr>";
        }
        ?>
    </table>
</div>

<?php include "php/footerbottom.php"; ?>
