<?php
// Database connection details
$databaseHost = 'localhost';
$databaseUsername = 'root';
$databasePassword = '';
$dbname = "spes_db";

// Create a connection to the database
$conn = new mysqli($databaseHost, $databaseUsername, $databasePassword, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM applicants WHERE status = 'approved'";
$result = $conn->query($sql);

if (!$result) {
    die("Error in SQL query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>eSPES | Applicants' List</title>
    <link href="bootstrap.css" rel="stylesheet">
    <link href="custom.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link rel="shortcut icon" type="x-icon" href="spes_logo.png">
    <style>
     
        .container1 {
            width: 75%;
            margin: 0 auto;
            padding: 50px;
        
        }
        h2, h3 {
            text-align: left;
            color: white;
            font-size: 24px; /* Adjust the font size here */
        }

        .qua {
            text-align: left;
            margin-left: 20px;
            font-size: 18px; /* Adjust the font size here */
            color: #F3EEEA;
        }
    </style>
</head>


<?php include('header.php'); ?>

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
                            <span>Welcome, SPES Information</span>
                            <h2></h2>
                        </div>
                    </div>
                    <!-- /menu profile quick info -->
                    <br />
                    <!-- sidebar menu -->
                    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                        <div class="menu_section">
                            <h3>SPES Menu</h3>
                            <ul class="nav side-menu">
                                <li><a href="about.php"><i class="fa fa-bars"></i> About SPES</a></li>
                                <li><a href="quali.php"><i class="fa fa-bars"></i> Qualifications</a></li>
                                <li><a href="flow.php"><i class="fa fa-bars"></i> Flow of Application Process</a></li>
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

            <div class="container1">
            <h2>Qualifications</h2>
    <ul class="qua">
        <li>18 to 30 years old</li>
        <li>Resident of Batangas City</li>
        <li>NOT a Recipient of EBD Scholarship Program</li>
    </ul>

    <h3>Initial Requirements</h3>
    <ul class="qua">
        <li>Photocopy of recent Report Card</li>
        <li>Latest Income Tax Return of Parents or Certificate of Indigency</li>
        <li>Birth Certificate</li>
        <li>Photocopy of School ID</li>
        <li>Certificate of Non-Enrollment from Barangay Captain for OUT OF SCHOOL YOUTH</li>
    </ul>
</div>
 


                <!-- footer content -->
                  <!-- footer content -->
        <footer id="mainFooter" style="position: fixed; bottom: 0; left: 0; width: 88%">
            &copy; Copyright 2023 | Online Special Program for Employment of Student (SPES)
        </footer>

                <!-- /footer content -->
            </div>
        </div>
    </div>

    <!-- Custom Theme Scripts -->
    <script src="custom.js"></script>
</body>

</html>
