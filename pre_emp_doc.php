<?php
session_start();
// Database connection details
$databaseHost = 'localhost';
$databaseUsername = 'root';
$databasePassword = '';
$dbname = 'spes_db';

// Create a database connection
$conn = new mysqli($databaseHost, $databaseUsername, $databasePassword, $dbname);

// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Initialize the $uploadResults array
$uploadResults = [
    'school_id_photo' => null,
    'birth_certificate' => null,
    'e_signature' => null,
    'photo_grades' => null,
    'photo_itr' => null
];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Define the target directory for file uploads
    $targetDirectory = "uploads/";

    // Loop through the uploaded files
    foreach ($_FILES as $fieldName => $file) {
        $targetFile = $targetDirectory . uniqid() . "_" . basename($file["name"]);

        // Check if the file upload was successful
        if ($file['error'] === UPLOAD_ERR_OK) {
            // Upload the file to the server
            if (move_uploaded_file($file["tmp_name"], $targetFile)) {
                $uploadResults[$fieldName] = $targetFile; // Store the file path in the array
            } else {
                $uploadResults[$fieldName] = "File upload failed.";
            }
        } else {
            $uploadResults[$fieldName] = "File upload failed with error code: " . $file['error'];
        }
    }

    // Now, you can handle the database insertion as per your requirements
    // Include the 'user_id' in the INSERT statement
    $user_id = $_SESSION['user_id'];
    $sql = "INSERT INTO applicant_documents (user_id, school_id_photo, birth_certificate, e_signature, photo_grades, photo_itr) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Check if each file path exists in the uploadResults array before binding
    if (
        isset($uploadResults['school_id_photo']) &&
        isset($uploadResults['birth_certificate']) &&
        isset($uploadResults['e_signature']) &&
        isset($uploadResults['photo_grades']) &&
        isset($uploadResults['photo_itr'])
    ) {
        $stmt->bind_param(
            "ssssss",
            $user_id,
            $uploadResults['school_id_photo'],
            $uploadResults['birth_certificate'],
            $uploadResults['e_signature'],
            $uploadResults['photo_grades'],
            $uploadResults['photo_itr']
        );

        if ($stmt->execute()) {
            // Data inserted successfully
            header("Location: submitted.php");
            exit; // Make sure to exit to prevent further script execution
        } else {
            // Error occurred while inserting data
            echo "Error: " . $stmt->error;
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        echo "File paths are missing in the uploadResults array.";
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>eSPES | Applicant Home Page</title>
    <!-- Bootstrap -->
    <link href="bootstrap.css" rel="stylesheet">
    <!-- Emmet -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/emmet/2.3.4/emmet.cjs.js" integrity="sha512-/0TtlmFaP6kPAvDm5nsVp86JQQ1QlfFXaLV9svYbZNtGUSSvi7c3FFSRrs63B9j6iM+gBffFA3AcL4V3mUxycw==" crossorigin="anonymous"></script>
    <!-- Custom Theme Style -->
    <link href="custom.css" rel="stylesheet">
    <!-- jQuery UI -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
        body {
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
                        <span>Welcome, <br>Applicant</br></span>
                        <h2></h2>
                    </div>
                </div>
                <!-- /menu profile quick info -->
                <br/>
                <!-- sidebar menu -->
                <div id="sidebar-menu" class="c">
                    <div class="menu_section">
                        <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                        <h3>SPES Applicant Menu</h3>
                        <ul class="nav side-menu">
                            <li><a id="menu_toggle"><i class="fa fa-bars"></i> My Profile</a>
                            <li><a id="menu_toggle"><i class="fa fa-bars"></i> Required Docs. </a>
                            <li><a id="menu_toggle"><i class="fa fa-bars"></i> Submitted. </a>
   
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
            <li><a href="http://localhost/Capstone/index.php"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
        </ul>
    </nav>
</div>
</div>
<!-- /top navigation -->

<div id="loader"></div>

<!-- page content -->
<div id="mainContent" class="right_col" role="main">

<!-- page content -->
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2><small>Please upload required files</small></h2>
                <div class="separator my-10"></div>
                <div class="clearfix"></div> <br>
            </div>
            <div class="x_content">
                <div class="alert alert-warning alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <b>Warning!</b> You cannot make any changes to these documents once your application is approved.
                </div>

                <div class="separator my-10"></div>

                <div hidden id="alertMessage" class="alert alert-success alert-dismissible fade in"><i class="glyphicon glyphicon-question-sign"></i> </div>
                <form id="formPhoto" data-parsley-validate class="form-horizontal form-label-left" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="photo_id">School ID (Scanned Image):<span class="required">*</span></label>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                        <input type="file" name="school_id_photo" id="photo_id" style="margin-top: 7px;" accept=".jpg,.jpeg,.png,.pdf" />
                        </div>
                        <div id="uploaded_image_school_id" class="col-md-3 col-sm-6 col-xs-12">
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 30px;">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="photo_birthcert">Birth Certificate/Gov. issued ID (PDF File / Scanned Image):<span class="required">*</span></label>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                        <input type="file" name="birth_certificate" id="photo_birthcert" style="margin-top: 7px;" accept=".jpg,.jpeg,.pdf" />
                        </div>
                        <div id="uploaded_image_birth_cert" class="col-md-3 col-sm-6 col-xs-12">
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 30px;">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="photo_esign"> 3E-Signature (Scanned Image):<span class="required">*</span></label>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                        <input type="file" name="e_signature" id="photo_esign" style="margin-top: 7px;" accept=".jpg,.jpeg,.png,.pdf" />
                        </div>
                        <div id="uploaded_image_signature" class="col-md-3 col-sm-6 col-xs-12">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="photo_grades">Grades/Cert. OSY:<span class="required">*</span></label>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                        <input type="file" name="photo_grades" id="photo_grades" required="required" style="margin-top: 7px;" accept=".jpg,.jpeg,.pdf" />
                        </div>
                        <div id="uploaded_image_grades" class="col-md-3 col-sm-6 col-xs-12">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="photo_itr">ITR/Cert. Indigency:<span class="required">*</span></label>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                        <input type="file" name="photo_itr" id="photo_itr" required="required" style="margin-top: 7px;" accept=".jpg,.jpeg,.pdf" />
                        </div>
                        <div id="uploaded_image_itr" class="col-md-3 col-sm-6 col-xs-12">
                        </div>
                    </div>
  
                    <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                            <br><br>
                            <button class="btn btn-primary" type="button" onclick="cancelEditProfile()">Cancel</button>
                            <button class="btn btn-warning" onclick="goBack()">Back</button>
                            <button class="btn btn-success" type="submit" name="next">Submit</button>
                            <br><br><br><br><br><br><br><br>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    // Function to handle form submission
    function submitForm() {
        // Perform any validation or checks here if needed

        // Submit the form
        document.getElementById("formPhoto").submit();
    }

    // Attach the submitForm function to the submit button's click event
    document.getElementById("next").addEventListener("click", function() {
        window.location.href = "submitted.php";
    });

    // Function to navigate back to the previous page
    function goBack() {
        window.location.href = "spes_profile.php";
    }

</script>

    <!-- footer content -->
    <footer id="mainFooter">
        <div class="pull-right">
            &copy; Copyright 2023 | Online Special Program for Employment of Student (SPES)
        </div>
        <div class="clearfix"></div>
    </footer>
    <!-- /footer content -->
</div>
</div>

</body>
</html>
