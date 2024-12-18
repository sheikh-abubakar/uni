<?php
session_start();
require "php/config.php";
require_once "php/functions.php";
$user = new login_registration_class();
$sid = $_SESSION['sid'];
$sname = $_SESSION['sname'];

if (!$user->getsession()) {
    header('Location: st_login.php');
    exit();
}

if (isset($_REQUEST['vr'])) {
    $stid = $_REQUEST['vr'];
    $name = $_REQUEST['vn'];
}

$pageTitle = "Student Result";
include "php/headertop.php";
?>

<div class="all_student fix">
    <?php
    // Custom functions to calculate credit hour and grade point
    function credit_hour($x) {
        switch ($x) {
            case "DBMS": return 3;
            case "DBMS Lab": return 1;
            case "Mathematics": return 4;
            case "Programming": return 3;
            case "Programming Lab": return 1;
            case "English": return 4;
            case "Physics": return 3;
            case "Chemistry": return 3;
            case "Psychology": return 3;
            default: return 0;
        }
    }

    function grade_point($gd) {
        if ($gd < 60) return 0;
        elseif ($gd >= 60 && $gd < 70) return 1;
        elseif ($gd >= 70 && $gd < 80) return 2;
        elseif ($gd >= 80 && $gd < 90) return 3;
        elseif ($gd >= 90 && $gd <= 100) return 4;
    }
    ?>

    <!-- Student Information -->
    <div class="fix">
        <p style="text-align:center;color:#fff;background:purple;margin:0;padding:8px;">
            <?php echo "Name: " . $name . "<br>Student ID: " . $stid; ?>
        </p>
    </div>
    
    <!-- Result Table -->
    <div class="fix">
        <p style='text-align:center;background:#ddd;color:#01C3AA;padding:5px;width:84%;margin:0 auto'>
            All Completed Courses & Grades
        </p>
    </div>

    <?php
    // Fetch results
    $i = 0;
    $ch = 0;
    $gp = 0;

    $get_result = $user->view_cgpa($stid);
    
    if ($get_result && ($get_result->num_rows) > 0) {
    ?>
        <table class="tab_two" style="text-align:center;width:85%;margin:0 auto">
            <th>Subject</th>
            <th>Marks</th>
            <th>Grade</th>
            <th>Credit hr.</th>
            <th>Status</th>

        <?php
        while ($rows = $get_result->fetch_assoc()) {
            $i++;
            $ch += credit_hour($rows['sub']);
            $grade = $rows['marks'];
            ?>
            <tr>
                <td><?php echo $rows['sub']; ?></td>
                <td><?php echo $grade; ?></td>
                <td>
                    <?php
                    if ($grade < 60) echo "F";
                    elseif ($grade >= 60 && $grade < 70) echo "D";
                    elseif ($grade >= 70 && $grade < 80) echo "C";
                    elseif ($grade >= 80 && $grade < 90) echo "B";
                    elseif ($grade >= 90 && $grade <= 100) echo "A";

                    $gp += credit_hour($rows['sub']) * grade_point($rows['marks']);
                    ?>
                </td>
                <td><?php echo credit_hour($rows['sub']); ?></td>
                <td>
                    <?php
                    if ($grade < 60) {
                        echo "<span style='background:red;padding:3px 11px;color:#fff;'>Fail</span>";
                    } elseif ($grade >= 60 && $grade < 70) {
                        echo "<span style='background:yellow'>Retake</span>";
                    } else {
                        echo "<span style='background:green;padding:3px 6px;color:#fff;'>Pass</span>";
                    }
                    ?>
                </td>
            </tr>
        <?php } ?>

        <!-- CGPA Calculation -->
        <tr>
            <td><?php echo "Total Course: <span style='color:#800080;padding:3px 6px;font-size:22px'>" . $i . "</span>"; ?></td>
            <td colspan="1">Total CGPA : </td>
            <td colspan="2">
                <?php
                $sg = $gp / $ch;
                echo "<span style='color:green;padding:3px 6px;font-size:22px'>" . round($sg, 2) . "</span>";
                ?>
            </td>
            <td>
                <?php
                if ($sg >= 3.5) {
                    echo "<span style='background:purple;padding:3px 6px;color:#fff;'>Excellent";
                } elseif ($sg >= 3.0 && $sg < 3.5) {
                    echo "<span style='background:green;padding:3px 6px;color:#fff;'>Good";
                } elseif ($sg >= 2.5 && $sg < 3.0) {
                    echo "<span style='background:gray;padding:3px 6px;color:#fff;'>Average";
                } else {
                    echo "<span style='background:red;padding:3px 6px;color:#fff;'>Probation";
                }
                ?>
            </td>
        </tr>
        </table>

    <?php } else {
        echo "<p style='color:red;text-align:center'>Nothing Found</p>";
    } ?>

    <!-- Back and Download Transcript Buttons -->
    <div class="back fix" style="margin-top: 20px;">
        <p style="text-align:center">
            <a href="view_single_result.php?vr=<?php echo $stid; ?>&vn=<?php echo $name; ?>">
                <button class="editbtn">Go to result page</button>
            </a>
        </p>
        <p style="text-align:center">
            <a href="download_transcript.php?sid=<?php echo $stid; ?>" target="_blank">
                <button class="editbtn" style="background:#3498db;color:#fff;width:180px;border-radius:5px;margin-top:10px;">
                    Download Transcript
                </button>
            </a>
        </p>
    </div>

</div>

<?php include "php/footerbottom.php"; ?>
<?php ob_end_flush(); ?>
