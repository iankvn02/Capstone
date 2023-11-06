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

// Create 'users' table if it doesn't exist
$createTableQuery = "CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    lname VARCHAR(255) NOT NULL,
    gname VARCHAR(255) NOT NULL,
    mname VARCHAR(255),
    email VARCHAR(255) NOT NULL,
    gender VARCHAR(10) NOT NULL,
    contact_number VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL
)";

if ($conn->query($createTableQuery) === FALSE) {
    echo "Error creating table: " . $conn->error;
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Retrieve user inputs from the form
    $mobile = $_POST['mobile_no'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $last_Name = $_POST['last_Name'];
    $first_Name = $_POST['first_Name'];
    $middle_Name = $_POST['middle_Name'];
    $sex = $_POST['sex'];
    $username = $_POST['username'];

    // Check if the email already exists
    $checkEmailQuery = "SELECT user_id FROM users WHERE email = '$email'";
    $emailResult = $conn->query($checkEmailQuery);

    if ($emailResult->num_rows > 0) {
        echo '<script>alert("Error: Email already exists.");</script>';
    } else {
        // Prepare an SQL statement to insert user data into the database
        $sql = "INSERT INTO users (lname, gname, mname, email, gender, contact_number, password) 
                VALUES ('$last_Name', '$first_Name', '$middle_Name', '$email', '$sex', '$mobile', '$password')";

        // Execute the SQL statement
        if ($conn->query($sql) === TRUE) {
            echo '<script>alert("You have successfully registered for the SPES program.");</script>';
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
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
    <title>eSPES | Sign up</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&amp;display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/4.1.0/mdb.min.css" rel="stylesheet">
    <link rel="shortcut icon" type="x-icon" href="spes_logo.png">
    <link href="style.css" rel="stylesheet">
</head>
<body data-new-gr-c-s-check-loaded="14.1121.0" data-gr-ext-installed="">
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
                            <form id="aep" method="post">
                                <div class="d-flex align-items-center mb-3 pb-1">
                                    <img src="dole-logo.png" class="img-fluid" style="width: 100px !important;" alt="Phone image">
                                    <span class="h1 fw-bold mb-0">Register</span>
                                </div>
                                <div class="input-box">
                                    <div class="icon"><i class="fas fa-user-alt trailing"></i></div>
                                    <input type="text" id="username" name="username" class="form-control form-control-lg border form-icon-trailing" required="">
                                    <label class="form-label" for="username">Username</label>
                                </div>
                                <div class="input-box">
                                <div class="icon"><i class="fas fa-lock trailing"></i></div>
                                    <input type="password" id="password" name="password" class="form-control form-control-lg border form-icon-trailing" required="">
                                    <label class="form-label" for="password">Password</label>
                                </div>
                                <hr>
                                <div class="input-box">
                                <div class="icon"><i class="fas fa-align-left trailing"></i></div>
                                    <input type="text" id="first_Name" name="first_Name" class="form-control form-control-lg border form-icon-trailing" required="">
                                    <label class="form-label" for="first_Name">First Name</label>
                                </div>
                                <div class="input-box">
                                <div class="icon"><i class="fas fa-align-center trailing"></i></div>
                                    <input type="text" id="middle_Name" name="middle_Name" class="form-control form-control-lg border form-icon-trailing" required="">
                                    <label class="form-label" for="middle_Name">Middle Name</label>
                                </div>
                                <div class="input-box">
                                <div class="icon"><i class="fas fa-align-right trailing"></i></div>
                                    <input type="text" id="last_Name" name="last_Name" class="form-control form-control-lg border form-icon-trailing" required="">
                                    <label class="form-label" for="last_Name">Last Name</label>
                                </div>
                                <div class="input-box">
                                    <div class="icon"><i class="fas fa-caret-down trailing"></i></div>
                                        <select id="sex" name="sex" class="required form-control form-control-lg border form-icon-trailing">
                                            <option value="" selected="" disabled="">Sex:</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                    
                                </div>
                                <div class="input-box">
                                <div class="icon"><i class="fas fa-envelope trailing"></i></div>
                                    <input type="email" id="email" name="email" class="form-control form-control-lg border form-icon-trailing" required="">
                                    <label class="form-label" for="email">Email Address</label>
                                </div>
                                <!-- Submit button -->
                                <input type="submit" id="register_butt" class="btn btn-primary btn-lg btn-block" style="background-color: #3b5998" value="Sign Up">
                                <div class="pt-2"></div>
                                <div class="pt-1 mb-4">
                                    <div class="divider d-flex align-items-center my-4">
                                        <p class="text-center fw-bold mx-3 mb-0 text-muted">Already Registered?</p>
                                    </div>
                                    <a class="btn btn-primary btn-lg btn-block" style="background-color: #3b5998" href="index.php" role="button">
                                        <i class="far fa-user me-2"></i>
                                        Sign In
                                    </a>
                                </div>
                                <div class="divider d-flex align-items-center my-4">
                                    <a href="#!" class="small text-muted">Copyright © 2023 SPES . All Rights Reserved</a>
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
