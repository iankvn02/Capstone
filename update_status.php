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

    // Get the current status before updating
    $getCurrentStatusQuery = "SELECT status FROM applicants WHERE id = $applicantID";
    $result = $conn->query($getCurrentStatusQuery);
    $row = $result->fetch_assoc();
    $oldStatus = $row['status'];

    // Update the 'status' in the database
    $updateStatusQuery = "UPDATE applicants SET status = '$newStatus' WHERE id = $applicantID";
    if ($conn->query($updateStatusQuery) === TRUE) {
        // Log the change into the history table
        $logHistoryQuery = "INSERT INTO history (user_id, action, status, date) VALUES ('$applicantID', 'Status is set to $newStatus', '$newStatus', NOW())";
   

        if ($conn->query($logHistoryQuery) === TRUE) {
            echo 'success';
        } else {
            echo 'error logging history';
        }
    } else {
        echo 'error updating status';
    }

    // Close the database connection
    $conn->close();
} else {
    echo 'Invalid parameters';
}
?>
