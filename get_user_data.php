<?php
session_start();

// Database connection details
$databaseHost = 'localhost';
$databaseUsername = 'root';
$databasePassword = '';
$dbname = "spes_db";

// Create a new MySQLi connection
$conn = new mysqli($databaseHost, $databaseUsername, $databasePassword, $dbname);

// Check for a successful connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


    // Assuming 'applicants' is your table name and 'user_id' is the identifier column
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
     // Debugging: Echo the user ID

    // Prepare and execute query to fetch user data
    $stmt = $conn->prepare("SELECT * FROM applicants WHERE user_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            if ($result->num_rows > 0) {
                $userData = $result->fetch_assoc();
                // Return user data as JSON response
                header('Content-Type: application/json');
                echo json_encode($userData);
            } else {
                // If no user data found
                echo json_encode(array('error' => 'User data not found'));
            }
        } else {
            // If there's an issue with the result
            echo json_encode(array('error' => 'Error fetching result'));
        }

        // Close the result set
        $result->close();
        // Close the statement
        $stmt->close();
    } else {
        // If there's an issue with the prepared statement
        echo json_encode(array('error' => 'Error in database query'));
    }


// Close the database connection
$conn->close();
?>
