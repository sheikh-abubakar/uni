<!Doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title><?php echo isset($pageTitle) ? $pageTitle : 'University Management System'; ?></title>
        <meta name="description" content="University Management system">
        <meta name="author" content="Teen Bahadur">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="icon" type="image/png" href="favicons/favicon-96x96.png" sizes="96x96" />
        <link rel="icon" type="image/svg+xml" href="favicons/favicon.svg" />
        <link rel="shortcut icon" href="favicons/favicon.ico" />
        <link rel="apple-touch-icon" sizes="180x180" href="favicons/apple-touch-icon.png" />
        <meta name="apple-mobile-web-app-title" content="CMS" />
        <link rel="manifest" href="favicons/site.webmanifest" />

        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="fonts/stylesheet.css">
        <link rel="stylesheet" href="css/main.css">

        <link rel="stylesheet" href="plugins/file-uploader/css/jquery.fileupload.css">
        <link rel="stylesheet" href="plugins/file-uploader/css/jquery.fileupload-ui.css">
        <script src="js/vendor/jquery-1.12.0.min.js"></script>
        <script src="js/vendor/modernizr-2.8.3.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->
        <header class="container header_area">
            <div id="sticker">
                <div class="head">
                    <a href="#"><div class="logo fix">
                        <img src="img/logo.png" alt="" />
                    </div></a>
                    <div class="uniname fix">
                    <h2>University Management System</h2>
                    </div>
                </div>
                <div class="menu ">
                    <div class="dateshow fix"><p><?php echo "Date : " . date("d M Y"); ?></p></div>
                    <ul>
                        <?php if ($user->getsession()) { ?>
                        <li><a href="st_logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a></li>
                        <li><a href="st_change_pass.php"><i class="fa fa-sign-out" aria-hidden="true"></i> Change Password</a></li>
                        <li><a href="view_single_result.php?vr=<?php echo isset($sid) ? $sid : ''; ?>&vn=<?php echo isset($sname) ? $sname : ''; ?>"><i class="fa fa-sign-out" aria-hidden="true"></i> Result</a></li>
                        <li><a href="st_profile.php"><i class="fa fa-user" aria-hidden="true"></i> <?php echo isset($sid) ? $sid : ''; ?></a></li>
                        <?php } ?>
                        <?php if ($user->get_faculty_session()) { 
                            // Ensure $fname is set before trying to display it
                            $fname_display = isset($fname) ? $fname : 'Faculty';
                        ?>
                        <li><a href="facultylogout.php"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a></li>
                        <li><a href="class_att_fc.php"><i class="fa fa-cog" aria-hidden="true"></i> Options</a></li>
                        <li><a href="fct_single_profile.php"><i class="fa fa-user" aria-hidden="true"></i> <?php echo $fname_display; ?></a></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </header>
        <div class="info container fix">
