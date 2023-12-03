<?php
// Database connection details
$databaseHost = 'localhost';
$databaseUsername = 'root';
$databasePassword = '';
$dbname = "spes_db";

// Start a session (if not already started)
session_start();

// Create a database connection
$conn = new mysqli($databaseHost, $databaseUsername, $databasePassword, $dbname);

// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Initialize variables for form data
$firstName = $middleName = $lastName = $typeApplication = $mobileNo = $sex = '';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Fetch user data based on the user ID, including the email
$selectSql = "SELECT first_Name, middle_Name, last_Name, type_Application, mobile_no, sex, email FROM applicants WHERE user_id = ?";
$selectStmt = $conn->prepare($selectSql);
$selectStmt->bind_param("i", $user_id);

// Execute the query and check for errors
if ($selectStmt->execute()) {
    $selectResult = $selectStmt->get_result();
    if ($selectResult->num_rows > 0) {
        $userData = $selectResult->fetch_assoc();
    }
} else {
    echo "Query execution failed: " . $selectStmt->error;
}

// Close the prepared statement
$selectStmt->close();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>eSPES | Applicant </title>
    <link href="bootstrap.css" rel="stylesheet">
    <link href="custom.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link rel="shortcut icon" type="x-icon" href="spes_logo.png">
    <style>
        body, h2 {
            font-family: "Century Gothic", sans-serif;
        }
    </style>
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
                        <span>Welcome, <br> Applicant</br></span>
                        <h2> </h2>
                    </div>
                </div>
                <!-- /menu profile quick info -->
                <br />
                <!-- sidebar menu -->
                <div id="sidebar-menu" class="c">
  				<div class="menu_section">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                <h3>SPES Applicant Menu</h3>
	            <ul class="nav side-menu">
	            <li><a href= "#" id="menu_toggle"><i class="fa fa-bars"></i> My Profile</a>
                <li><a href= "#" id="menu_toggle"><i class="fa fa-bars"></i> Required Docs. </a>
                <li><a href= "#" id="menu_toggle"><i class="fa fa-bars"></i> Submitted. </a>
                <li><a href="history.php" id="menu_toggle"><i class="#"></i> History </a>
		            </ul>
	            </li>
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
        
        <div id="mainContent2" class="right_col " role="main">
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item "><a href="spes_profile.php">My Profile</a></li>
            <li class="breadcrumb-item " ><a href="pre_emp_doc.php">Required Docs</a></li>
            <li class="breadcrumb-item active" aria-current="page">Submitted</li>
          </ol>
        </nav>
          <h2 style="font-size: 20px;" >SPES Applicant</h2>
            <div class="separator my-10"></div>

            <div class="content fs-6 d-flex flex-column-fluid" id="kt_content">
  <div class="container-xxl">
    <div class="card mx-auto">
      <form class="form d-flex flex-right">
        <div class="card-body mw-800px py-20 d-flex flex-column align-items-center justify-content-center">
          <div class="row">
            <div class="col-lg-9 col-xl-6 mx-auto"> <!-- Center the title on all screen sizes -->
              <h5 class="fw-bold mb-6" style="font-size: 18px;">Overview</h5>
            </div>
          </div>

          <div class="row align-items-center">
            <label class="col-xl-4 col-lg-6 col-form-label fw-bold text-right text-lg-end">Name:</label>
            <div class="col-lg-5 col-xl-3 d-flex align-items-center fw-bold">
              <span id="first_Name" class="info-span" style="font-size: 16px;"><?php echo isset($userData['first_Name']) ? $userData['first_Name'] : '00'; ?></span>
              <span id="middle_Name" class="info-span" style="font-size: 16px;"><?php echo isset($userData['middle_Name']) ? $userData['middle_Name'] : '00'; ?></span>
              <span id="last_Name" class="info-span" style="font-size: 16px;"><?php echo isset($userData['last_Name']) ? $userData['last_Name'] : '00'; ?></span>
            </div>
          </div>

          <div class="row align-items-center">
              <label class="col-xl-6 col-lg-6 col-form-label fw-bold text-right text-lg-end">Type of Application:</label>
              <div class="col-lg-2 col-xl-3 mx-auto d-flex align-items-center fw-bold">
                  <span id="type_Application" class="info-span" style="font-size: 16px;"><?php echo isset($userData['type_Application']) ? $userData['type_Application'] : '00'; ?></span>
              </div>
          </div>

          <div class="row align-items-center">
              <label class="col-xl-6 col-lg-6 col-form-label fw-bold text-right text-lg-end">Contact:</label>
              <div class="col-lg-2 col-xl-3 mx-auto d-flex align-items-start fw-bold">
                  <span id="mobile_no" class="info-span" style="font-size: 16px;"><?php echo isset($userData['mobile_no']) ? $userData['mobile_no'] : '00'; ?></span>
              </div>
          </div>

          <div class="row align-items-center">
              <label class="col-xl-6 col-lg-6 col-form-label fw-bold text-right text-lg-end">Sex:</label>
              <div class="col-lg-2 col-xl-3 mx-auto d-flex align-items-center fw-bold">
                  <span id="sex" class="info-span" style="font-size: 16px;"><?php echo isset($userData['sex']) ? $userData['sex'] : '00'; ?></span>
              </div>
          </div>

          <div class="row align-items-center">
              <label class="col-xl-6 col-lg-6 col-form-label fw-bold text-right text-lg-end">Status:</label>
              <div class="col-lg-2 col-xl-3 mx-auto d-flex align-items-center fw-bold">
                  <span class="badge" style="font-size: 18px; background-color: #20d489; color: white;">Submitted</span>
              </div>
          </div>
                  <br>
                  <div class="separator my-10"></div>
                  <br><br>

                  <div class="text-center mb-10">
                  <h1 class="fs-2tx fw-bolder mb-5" style="font-size: 24px;">Your application has been submitted!
                      <span class="d-inline-block position-relative ms-2">
                      </span>
                  </h1>
          <div class="fw-bold fs-2 text-gray-500 mb-10" style="font-size: 18px;">
              We will reach out to you <span class="fw-bolder text-gray-900">via email (<span id="email">
                  <?php echo isset($userData['email']) ? $userData['email'] : 'N/A'; ?>
              </span>)</span> as soon as we have processed your application. Thank you!
          <br><br><br><br><br><br><br><br><br><br><br>
          </div>
        </div>
      </div>

</form></div>
</div>
</div>

        <!-- footer content -->
        <footer id="mainFooter">
            <!-- footer content -->
        <footer id="mainFooter" style="position: fixed; bottom: 0; left: 0; width: 88%">
            &copy; Copyright 2023 | Online Special Program for Employment of Student (SPES)
        </footer>

          <div class="clearfix"></div>
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
    </script> 

<!-- Custom Theme Scripts -->
<script src="custom.js"></script>
    
    <script>
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