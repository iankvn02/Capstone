<?php
// Include your database connection details here
include("conn.php");

if (isset($_POST['id'])) {
  $applicantID = $_POST['id'];

  // Create a connection to the database
  $conn = new mysqli($databaseHost, $databaseUsername, $databasePassword, $dbname);

  // Query to retrieve the email associated with the applicant's ID
  $sql = "SELECT email FROM applicants WHERE id = $applicantID";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $email = $row['email'];

    echo json_encode(['email' => $email]);
  } else {
    echo json_encode(['email' => '']);
  }
} else {
  echo json_encode(['email' => '']);
}
?>
