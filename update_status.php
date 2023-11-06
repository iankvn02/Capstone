<?php
if (isset($_POST['applicantID']) && isset($_POST['newStatus'])) {
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

    $applicantID = $_POST['applicantID'];
    $newStatus = $_POST['newStatus'];

    // Update the 'status' in the database
    $sql = "UPDATE applicants SET status = '$newStatus' WHERE id = $applicantID";
    if ($conn->query($sql) === TRUE) {
        echo 'success';
    } else {
        echo 'error';
    }

    // Close the database connection
    $conn->close();
} else {
    echo 'Invalid parameters';
}