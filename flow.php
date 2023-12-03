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
            <div class="container1">

            <h3>Flow of Application Process</h3>
    <ul class="qua">
        <li>Apply and pass initial documents through eSPES. An email will be sent for confirmation</li>
        <li>Wait for application approval notice for the next step.</li>
        <li>Attend the orientation followed by face-to-face application and interview with the assigned company.</li>
        <li>Wait for employment approval.</li>
        <li>Start of job!</li>
    </ul>

</div>
             

        <!-- footer content -->
        <footer id="mainFooter" style="position: fixed; bottom: 0; left: 0; width: 88%">
            &copy; Copyright 2023 | Online Special Program for Employment of Student (SPES)
        </footer>

        <!-- /footer content -->
      </div>
    </div>

    <!-- Custom Theme Scripts -->
    <script src="custom.js"></script>
</body>

</html>
