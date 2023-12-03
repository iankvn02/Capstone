<?php
include 'createdb.php';

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

// Check if the username and password match a record in the database
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emailOrUsername = $_POST["email_or_username"];
    $password = $_POST["password"];

    // You should perform proper validation and sanitization here

    $sql = "SELECT user_id FROM users WHERE (email = ? OR username = ?) AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $emailOrUsername, $emailOrUsername, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Successful login, redirect to appropriate page based on user_id presence
        session_start();
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['user_id'];

        // Check if user_id is present in 'applicants' table
        $applicantSql = "SELECT user_id FROM applicants WHERE user_id = ?";
        $applicantStmt = $conn->prepare($applicantSql);
        $applicantStmt->bind_param("s", $_SESSION['user_id']);
        $applicantStmt->execute();
        $applicantResult = $applicantStmt->get_result();

        if ($applicantResult->num_rows == 1) {
            // User is in 'applicants' table, check 'applicant_documents' next
            $documentSql = "SELECT user_id FROM applicant_documents WHERE user_id = ?";
            $documentStmt = $conn->prepare($documentSql);
            $documentStmt->bind_param("s", $_SESSION['user_id']);
            $documentStmt->execute();
            $documentResult = $documentStmt->get_result();

            if ($documentResult->num_rows == 1) {
                // User is in 'applicant_documents', proceed to 'submitted.php'
                header("Location: submitted.php");
                exit();
            } else {
                // User is in 'applicants' but not in 'applicant_documents', proceed to 'pre_emp_doc.php'
                header("Location: pre_emp_doc.php");
                exit();
            }
        } else {
            // User is not in 'applicants', proceed to 'spes_profile.php'
            header("Location: spes_profile.php");
            exit();
        }
    }
    // Add this condition for admin login
    elseif ($emailOrUsername === "admin" && $password === "admin") {
        // Admin login, proceed to admin_homepage.php
        session_start();
        $_SESSION['admin'] = true;
        header("Location: admin_homepage.php");
        exit();
    } else {
        echo '<script>alert("Invalid email or username or password.");</script>';
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="description" content="Online Special Program for Employment of Student">
    <meta name="keywords" content="Online SPES, DOLE, Department of Labor and Employment">
    <title>eSPES | Please Sign in</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&amp;display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/4.1.0/mdb.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/4.1.0/mdb.min.js"></script>
    <link rel="shortcut icon" type="x-icon" href="spes_logo.png">
    <link href="style.css" rel="stylesheet">

    <style>
        /* Additional styles for the modal */
        .modal-body {
            max-height: 500px;
            overflow-y: auto;
        }

        /* Hide the close button in the modal header */
        .modal-header .close {
            display: none;
        }
    </style>
</head>
<body>



<div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col col-xl-10">
            <div class="card" style="border-radius: 1rem;">
                <div class="row g-0">
                    <div class="col-md-6 col-lg-5 d-none d-md-block position-relative">
                        <div class="position-absolute top-50 start-50 translate-middle" style="width: 500px !important; margin-left: 70px !important">
                            <img src="spes_logo.png" class="img-fluid" alt="SPES Logo">
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-7 d-flex align-items-center">
                        <div class="card-body p-4 p-lg-5 text-black">
                            <div class="d-flex align-items-center mb-3 pb-1">
                                <img src="dole-logo.png" class="img-fluid" style="width: 100px !important;" alt="Phone image">
                                <span class="h1 fw-bold mb-0">Log In</span>
                            </div>
                            <!-- Login form -->
                            <form method="POST">
                                <!-- Email input -->
                                <div class="input-box">
                                    <div class="icon"><i class="fas fa-user-alt trailing"></i></div>
                                    <input type="text" id="email_or_username" name="email_or_username" class="form-control form-control-lg border form-icon-trailing" required>
                                    <label class="form-label" for="email_or_username">Email or Username</label>
                                </div>
                                <!-- Password input -->
                                <div class="input-box">
                                <div class="icon"><i class="fas fa-lock trailing"></i></div>
                                    <input type="password" id="password" name="password" class="form-control form-control-lg border form-icon-trailing" required>
                                    <label class="form-label" for="password">Password</label>
                                </div>
                                
                                <!-- Submit button -->
                                <button class="btn btn-primary btn-lg btn-block" type="submit" style="background-color: #1054d4">
                                    Login
                                </button>
                                <div class="pt-2"> </div>
                                <div class="pt-1 mb-4">
                                    <div class="divider d-flex align-items-center my-4">
                                        <p class="text-center fw-bold mx-3 mb-0 text-muted">OR</p>
                                    </div>
                                    <a class="btn btn-primary btn-lg btn-block" style="background-color: #3b5998" data-toggle="modal" data-target="#termsModal" role="button">
                                        <i class="far fa-user me-2"></i> Register
                                    </a>
                                    <div class="divider d-flex align-items-center my-4">
                                        <p class="text-center fw-bold mx-3 mb-0 text-muted">USER MANUAL</p>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mx-auto">
                                            <a class="btn btn-primary btn-lg btn-block" style="background-color: #3A8891" href="#" target="_blank" role="button">
                                                <i class="far fa-user me-2"></i>
                                                FOR SPES APPLICANTS
                                            </a>
                                        </div>
                                    </div>
                                </div>

    <div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <!-- No modal header with close button -->
            <div class="modal-body">
                <!-- Content of Terms and Conditions -->
                <h6 style="font-weight: bold; font-size: 22px; text-align: center;">Data Privacy Notice and Consent Form</h6>
                <br>
                <p style="text-align: justify; font-size: 18px;">
                    I hereby consent and authorize Special Program for Employment of Students (SPES) to collect, use, share, 
                    and disclose my personal data contained in my registration form and in other supporting documents for the purpose of verifying my identity, 
                    and to assess and evaluate my application for employment. 
                    I acknowledge that my personal data collected may be shared to SPES's partner companies/industries as well as to authorized individuals and/or entities to assist SPES and its partner companies/industries in the selection process for employment. 
                    I understand that in compliance with the Data Privacy Act (R.A. No. 10173), SPES will strive to keep my information private and confidential and will retain my information solely for the fulfilment of the aforementioned purposes.
                    <!-- Add the content of the Republic Act here -->
                </p>
                <!-- Add more terms and conditions if needed -->
            </div>
            <div class="modal-footer" style="background-color: #f8f9fa;"> <!-- Set the background color here -->
                <a href="index.php" class="btn btn-secondary" id="disagreeBtn" style="background-color: #808080; color: #ffffff;">Disagree</a> <!-- Set the background color and text color here -->
                <a href="signup.php" class="btn btn-primary">Agree</a>
            </div>
        </div>
    </div>
</div>


<!-- Add Bootstrap JS and Popper.js (you can replace the links with the latest versions) -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

<!-- Add your custom script -->
<script>
    document.getElementById('agreeBtn').addEventListener('click', function () {
        // Do any additional actions upon agreeing (e.g., submit a form, proceed with registration)
        // For now, just close the modal
        $('#termsModal').modal('hide');
    });

    document.getElementById('disagreeBtn').addEventListener('click', function () {
        // Do any additional actions upon disagreeing (if needed)
        // For now, just close the modal
        $('#termsModal').modal('hide');
    });
</script>
                                <div class="divider d-flex align-items-center my-4">
                                    <p class="text-center fw-bold mx-3 mb-0 text-muted">Copyright © 2023 SPES. All Rights Reserved</p>
                                    <a class="text-button" href="about.php">|   All About SPES</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
