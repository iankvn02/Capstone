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

// Function to compress PDF
function compressPDF($inputPath, $outputPath) {
    // Adjust Ghostscript path as needed
    $gsPath = 'C:/Program Files/gs/gs9.53.3/bin/gswin64c.exe'; // Example path for Windows
    $command = "$gsPath -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/ebook -dNOPAUSE -dBATCH -dQUIET -sOutputFile=$outputPath $inputPath";
    
    // Execute Ghostscript command
    exec($command);
}

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
        // Compress PDF files before storing in the database
        $compressedBirthCert = "compressed/" . uniqid() . "_birth_cert.pdf";
        compressPDF($uploadResults['birth_certificate'], $compressedBirthCert);

        // Compress other PDF files similarly...

        // Bind parameters for the prepared statement
        $stmt->bind_param(
            "ssssss",
            $user_id,
            $uploadResults['school_id_photo'],
            $compressedBirthCert,
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
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>eSPES | Applicant Home Page</title>
    <link href="bootstrap.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link href="custom.css" rel="stylesheet">
    <script src="jquery.js"></script>
    <script src="parsley.js"></script>
    <meta charset="utf-8">
    <link rel="shortcut icon" type="x-icon" href="spes_logo.png">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pica/5.1.0/pica.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/image-similarity/2.2.0/image-similarity.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        // Function to check image blurriness
        function isImageBlurred(img) {
            // Create a canvas to draw the image
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.width = img.width;
            canvas.height = img.height;
            context.drawImage(img, 0, 0, canvas.width, canvas.height);

            // Convert canvas data to base64
            const dataUrl = canvas.toDataURL('image/jpeg', 0.1); // Lower quality may amplify blurriness

            // Check if the image is too blurry
            const image = new Image();
            image.src = dataUrl;
            const diffThreshold = 10; // Adjust this value for your specific needs

            return new Promise((resolve) => {
                image.onload = function () {
                    const imgData = context.getImageData(0, 0, canvas.width, canvas.height);
                    const diff = pixelDiff(imgData.data, this.width, this.height);
                    resolve(diff <= diffThreshold);
                };
            });
        }

        // Function to calculate pixel difference
        function pixelDiff(data, width, height) {
            let diff = 0;

            for (let i = 0; i < data.length; i += 4) {
                const r1 = data[i];
                const g1 = data[i + 1];
                const b1 = data[i + 2];

                const r2 = data[i + 4];
                const g2 = data[i + 5];
                const b2 = data[i + 6];

                diff += Math.abs(r1 - r2) + Math.abs(g1 - g2) + Math.abs(b1 - b2);
            }

            return diff / (width * height);
        }

        // Function to handle file input change
        function handleFileInputChange(inputId) {
            const fileInput = document.getElementById(inputId);
            const warningMessage = document.getElementById('warningMessage');
            const submitButton = document.getElementById('submitBtn');

            if (fileInput.files.length === 0) {
                warningMessage.innerText = '';
                submitButton.disabled = false;
                return;
            }

            const file = fileInput.files[0];
            const img = new Image();
            const URL = window.URL || window.webkitURL;
            img.src = URL.createObjectURL(file);

            img.onload = function () {
                if (img.width <= 800 || img.height <= 600) {
                    warningMessage.innerText = 'Image dimensions should be at least 800x600 pixels.';
                    submitButton.disabled = true;
                } else {
                    isImageBlurred(this).then(function (blurred) {
                        if (blurred) {
                            warningMessage.innerText = 'The uploaded image is too blurry.';
                            submitButton.disabled = true;
                        } else {
                            warningMessage.innerText = '';
                            submitButton.disabled = false;
                        }
                    });
                }
            };
        }

        // Attach event listeners to file input elements
        document.getElementById('photo_id').addEventListener('change', () => handleFileInputChange('photo_id'));
        document.getElementById('photo_birthcert').addEventListener('change', () => handleFileInputChange('photo_birthcert'));
        document.getElementById('photo_esign').addEventListener('change', () => handleFileInputChange('photo_esign'));
        document.getElementById('photo_grades').addEventListener('change', () => handleFileInputChange('photo_grades'));
        document.getElementById('photo_itr').addEventListener('change', () => handleFileInputChange('photo_itr'));
    });
</script>
<!-- Add this JavaScript code in the head section of your HTML -->
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
                        <li><a href="#"><i class="fa fa-bars"></i> My Profile</a></li>
                        <li><a href="#"><i class="fa fa-bars"></i> Required Docs.</a></li>
                        <li><a href="#"><i class="fa fa-bars"></i> Submitted.</a></li>
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


<div id="loader"></div>

<!-- page content -->
<div id="mainContent" class="right_col" role="main">
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item "><a href="spes_profile.php">My Profile</a></li>
            <li class="breadcrumb-item active" aria-current="page">Required Docs</li>
          </ol>
   </nav>
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
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="photo_esign"> PDF Files Only<span class="required">*</span></label>
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="photo_esign"> (Note: The PDF file size must not exceed 5 megabytes(mb).)</label>
                    <br>

                    <div class="form-group" style="margin-top: 30px;">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="photo_birthcert">Birth Certificate/Gov. issued ID (PDF File):<span class="required">*</span></label>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                        <input type="file" name="birth_certificate" id="photo_birthcert"  required="required" style="margin-top: 7px;" accept=".jpg,.jpeg,.pdf" />
                        </div>
                        <div id="uploaded_image_birth_cert" class="col-md-3 col-sm-6 col-xs-12">
                        <div id="birthCertSize"></div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="photo_grades">Grades/Cert. OSY:<span class="required">*</span></label>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                        <input type="file" name="photo_grades" id="photo_grades"  required="required" style="margin-top: 7px;" accept=".jpg,.jpeg,.pdf" />
                        </div>
                        <div id="uploaded_image_grades" class="col-md-3 col-sm-6 col-xs-12">
                        <div id="gradesSize"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="photo_itr">ITR/Cert. Indigency:<span class="required">*</span></label>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                        <input type="file" name="photo_itr" id="photo_itr"  required="required" style="margin-top: 7px;" accept=".jpg,.jpeg,.pdf" />
                        </div>
                        <div id="uploaded_image_itr" class="col-md-3 col-sm-6 col-xs-12">
                        <div id="itrSize"></div>
                        </div>
                    </div>
                    <br>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="photo_id">School ID (Scanned Image/PDF file):<span class="required">*</span></label>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                        <input type="file" name="school_id_photo" id="photo_id"  required="required" style="margin-top: 7px;" accept=".jpg,.jpeg,.png,.pdf" />
                        </div>
                        <div id="uploaded_image_school_id" class="col-md-3 col-sm-6 col-xs-12">
                        <div id="IDSize"></div>
                        </div>
                    </div>
                    

                   <br>
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="photo_esign"> Images Only <span class="required">*</span></label>
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="photo_esign"> (Note:  Image dimensions should be at least 800x600 pixels.)</label>
                    <br>
                    
                 
                    <div class="form-group" style="margin-top: 30px;">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="photo_esign"> 3E-Signature (Scanned Image):<span class="required">*</span></label>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                        <input type="file" name="e_signature" id="photo_esign" style="margin-top: 7px;" required="required" accept=".jpg,.jpeg,.png,.pdf" />
                        </div>
                        <div id="uploaded_image_signature" class="col-md-3 col-sm-6 col-xs-12">
                        </div>
                    </div>
  
                    <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                            <br>
                            <div id="warningMessage" class="alert alert-warning" role="alert"></div>
                            <button class="btn btn-primary" type="button" onclick="cancelEditProfile()">Cancel</button>
                            <button class="btn btn-warning" onclick="goBack()">Back</button>
                            <button class="btn btn-success" type="submit" name="next" id="submitBtn">Submit</button>
                            <br><br>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    // Function to display file size
    function displayFileSize(inputId, resultId) {
        const fileInput = document.getElementById(inputId);
        const resultElement = document.getElementById(resultId);

        if (fileInput.files.length === 0) {
            resultElement.innerText = '';
            return;
        }

        const file = fileInput.files[0];
        const fileSizeInBytes = file.size;
        const fileSizeInKB = fileSizeInBytes / 1024;
        const fileSizeInMB = fileSizeInKB / 1024;

        resultElement.innerText = `File Size: ${fileSizeInBytes} bytes (${fileSizeInKB.toFixed(2)} KB, ${fileSizeInMB.toFixed(2)} MB)`;
    }

    // Attach event listeners to file input elements
    document.getElementById('photo_birthcert').addEventListener('change', () => displayFileSize('photo_birthcert', 'birthCertSize'));
    document.getElementById('photo_grades').addEventListener('change', () => displayFileSize('photo_grades', 'gradesSize'));
    document.getElementById('photo_itr').addEventListener('change', () => displayFileSize('photo_itr', 'itrSize'));
    document.getElementById('photo_id').addEventListener('change', () => displayFileSize('photo_id', 'IDSize'));
    // Add more event listeners for other file inputs as needed
</script>


<script>
    // Function to display file size
    function displayFileSize(inputId, resultId) {
        const fileInput = document.getElementById(inputId);
        const resultElement = document.getElementById(resultId);
        const submitButton = document.getElementById('submitBtn'); // Get the submit button element

        if (fileInput.files.length === 0) {
            resultElement.innerText = '';
            submitButton.disabled = false;
            return;
        }

        const file = fileInput.files[0];
        const fileSizeInBytes = file.size;
        const fileSizeInKB = fileSizeInBytes / 1024;
        const fileSizeInMB = fileSizeInKB / 1024;

        resultElement.innerText = `File Size: ${fileSizeInBytes} bytes (${fileSizeInKB.toFixed(2)} KB, ${fileSizeInMB.toFixed(2)} MB)`;

        // Check if file size exceeds 5 MB and disable the submit button
        if (fileSizeInMB > 5) {
            warningMessage.innerText = 'File Size Exceeded 5mb.';
                    submitButton.disabled = true;
        } else {
            submitButton.disabled = false;
        }
    }

    // Attach event listeners to file input elements
    document.getElementById('photo_birthcert').addEventListener('change', () => displayFileSize('photo_birthcert', 'birthCertSize'));
    document.getElementById('photo_grades').addEventListener('change', () => displayFileSize('photo_grades', 'gradesSize'));
    document.getElementById('photo_itr').addEventListener('change', () => displayFileSize('photo_itr', 'itrSize'));
    // Add more event listeners for other file inputs as needed
</script>


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
            &copy; Copyright 2023 | Online Special Program for Employment of Student (SPES)
        </footer>
    <!-- /footer content -->
</div>
</div>

</body>
</html>