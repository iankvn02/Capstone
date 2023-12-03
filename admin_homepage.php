<!--COUNTER WITH CONTACT-->
<?php
$databaseHost = 'localhost';
$databaseUsername = 'root';
$databasePassword = '';
$dbname = "spes_db";

$conn = new mysqli($databaseHost, $databaseUsername, $databasePassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sqlCount = "SELECT COUNT(*) AS total FROM applicants WHERE type_Application = 'Renewal'";
$resultCount = $conn->query($sqlCount);

if ($resultCount) {
    $row = $resultCount->fetch_assoc();
    $totalRenewal = $row['total'];
} else {
    $totalRenewal = 0;
}
?>
<!--COUNTER WITH NO CONTACT-->
<?php
$databaseHost = 'localhost';
$databaseUsername = 'root';
$databasePassword = '';
$dbname = "spes_db";

$conn = new mysqli($databaseHost, $databaseUsername, $databasePassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sqlCount = "SELECT COUNT(*) AS total FROM applicants WHERE type_Application = 'New Applicants'";
$resultCount = $conn->query($sqlCount);

if ($resultCount) {
    $row = $resultCount->fetch_assoc();
    $totalNew = $row['total'];
} else {
    $totalNew = 0;
}
?>
<!--COUNTER MATERLIST-->
<?php
$databaseHost = 'localhost';
$databaseUsername = 'root';
$databasePassword = '';
$dbname = "spes_db";

$conn = new mysqli($databaseHost, $databaseUsername, $databasePassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sqlCount = "SELECT COUNT(*) AS total FROM applicants WHERE status = 'approved'";
$resultCount = $conn->query($sqlCount);

if ($resultCount) {
    $row = $resultCount->fetch_assoc();
    $totalApplicants = $row['total'];
} else {
    $totalApplicants = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> eSPES | Admin Homepage </title>
    <link href="bootstrap.css" rel="stylesheet">
    <link href="custom.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link rel="shortcut icon" type="x-icon" href="spes_logo.png">
</head>

<body class="nav-md">
<div class="container body">
    <div class="main_container">
        <div class="col-md-3 left_col">
            <div class="left_col scroll-view">
                <!-- menu profile quick info -->
                <div class="profile clearfix">
                    <div class="profile_pic">
                        <img src="spes_logo.png" alt="photo" class="img-circle profile_img">
                    </div>
                    <div class="profile_info">
                        <span>Welcome, <br>SPES Admin</br></span>
                        <h2> </h2>
                    </div>
                </div>
                <!-- /menu profile quick info -->
                <br />
                <!-- sidebar menu -->
                <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                    <div class="menu_section">
                        <h3>SPES Admin Menu</h3>
                        <ul class="nav side-menu">
                                <li><a href="admin_homepage.php"><i class="fa fa-bars"></i> Applicants</a></li>
                                <li><a href="admin_applicants.php"><i class="fa fa-bars"></i> Applicants' List</a></li>
                                <li><a href="admin_list.php"><i class="fa fa-bars"></i> Approved Applicants</a></li>
                                <li><a href="admin_decline.php"><i class="fa fa-bars"></i> Declined Applicants</a></li>
                                <li><a href="admin_archive.php"><i class="fa fa-bars"></i> Archived Applicants</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- /top navigation -->
        <div id="mainTopNav" class="top_nav">
            <div class="nav_menu">
                <nav>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="index.php"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                    </ul>
                </nav>
            </div>
        </div>
        <!-- /top navigation -->

        <div id="loader"></div>

        <!-- page content -->
        <div id="mainContent" class="right_col" role="main">
            <h2>SPES Admin</h2>
            <div class="box-container">
                <!-- Box 1 -->
                <div class="box">
                    <h2>Total No. SPES Babies</h2>
                    <p><?= $totalApplicants ?></p>
                </div>
                <!-- Box 2 -->
                <div class="box">
                    <h2>Total No. of New Applicants</h2>
                    <p><?= $totalNew ?></p>
                </div>
                <!-- Box 3 -->
                <div class="box">
                    <h2>Total No. of Renewal</h2>
                    <p><?= $totalRenewal ?></p>
                </div>
            </div>
        </div>

        
        <!-- /page content -->

        <!-- footer content -->
        <footer id="mainFooter">
            &copy; Copyright 2023 | Online Special Program for Employment of Student (SPES)
        </footer>
        <!-- /footer content -->
    </div>
</div>

<script>
    var myVar;

    function myFunction() {
        myVar = setTimeout(showPage, 3000);
    }

    function showPage() {
        document.getElementById("loader").style.display = "none";
        document.getElementById("mainContent").style.display = "block";
    }

    $(document).ready(function () {
        // Toggle sidebar
        $('#menu_toggle').click(function () {
            if ($('body').hasClass('nav-md')) {
                $('body').removeClass('nav-md').addClass('nav-sm');
            } else {
                $('body').removeClass('nav-sm').addClass('nav-md');
            }
        });
    });
</script>

</body>
</html>