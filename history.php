<?php
// Database connection details
$databaseHost = 'localhost';
$databaseUsername = 'root';
$databasePassword = '';
$dbname = "spes_db";
session_start();
// Create a connection to the database
$conn = new mysqli($databaseHost, $databaseUsername, $databasePassword, $dbname);

// Check the connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$sql = "SELECT * FROM history where user_id = $user_id";
$result = $conn->query($sql);

if (!$result) {
    die("Error in SQL query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>eSPES | Applicants' List</title>
    <link href="bootstrap.css" rel="stylesheet">
    <link href="custom.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link rel="shortcut icon" type="x-icon" href="spes_logo.png">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
<style>
  
.container2 {
    width: 100%;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

form h3 {
    color: #555;
    font-weight: 800;
    margin-bottom: 20px;
}
.email{
  background: white;
    display: flex;
    flex-direction: column;
    padding: 2vw 4vw;
    width: 90%;
    max-width: 600px;
    border-radius: 10px;
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
                        <span>Welcome, <br>Applicant</br></span>
                        <h2> </h2>
                    </div>
                </div>
                <!-- /menu profile quick info -->
                <br />
                <!-- sidebar menu -->
                <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
  				 <div class="menu_section">
					<a id="menu_toggle"><i class="#"></i></a>
						<h3>SPES Applicant Menu</h3>
						<ul class="nav side-menu">
						<li><a href="#" id="menu_toggle"><i class="#"></i> My Profile</a>
						<li><a href="#" id="menu_toggle"><i class="#"></i> Required Docs. </a>
						<li><a href="submitted.php" id="menu_toggle"><i class="#"></i> Submitted. </a>
						<li><a href="#" id="menu_toggle"><i class="#"></i> History </a>
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
              <h2> SPES Applicant </h2>
              <?php
include("conn.php");

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search = $_POST["search"]; // Get the search term from the form

    // Create a connection to the database
    $conn = new mysqli($databaseHost, $databaseUsername, $databasePassword, $dbname);
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    // Query to search applicants based on name or email
    $sql = "SELECT * FROM history WHERE  user_id = $user_id AND id LIKE '%$search%'";
    $result = $conn->query($sql);

    if (!$result) {
        die("Error in SQL query: " . $conn->error);
    }
} else {
    // Query to fetch all applicants when the form is not submitted
    $sql = "SELECT * FROM history where user_id = $user_id";
    $result = $conn->query($sql);

    if (!$result) {
        die("Error in SQL query: " . $conn->error);
    }
}
?>


<form class="search-form" method="POST" action="">
  <input class="search-input" type="text" name="search" placeholder="Search History ID">
</form>
<div class="box-container row box-b"> 
    <?php if ($result->num_rows > 0) : ?>
        <table class="content-table">
            <thead>
                <tr>
                    <th>History ID</th>
                    <th>Action</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr class="table-row" data-applicant-id="<?= $row['id'] ?>">
                        <td><?php echo str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?></td>
                        <td><?= $row['action'] ?></td>
                        <td><?= $row['status'] ?></td>
                        <td> <?php
                            $date = new DateTime($row['date']);
                            echo $date->format('F d, Y h:i A');
                        ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  function Decline() {
    // Use the PHP variable $recipientEmail to populate the 'To' field
    Email.send({
      Host: "smtp.elasticemail.com",
      Username: "batangascity.spes@gmail.com",
      Password: "13601B6261F0836EF26380F07D866D7D792B",
      To: '<?php echo $recipientEmail; ?>', // Populate 'To' with the fetched email
      From: "batangascity.spes@gmail.com",
      Subject: "ESPES APPLICANT UPDATE",
      Body: "Phone Number:" + document.getElementById("phone").value +
        "<br> Message:" + document.getElementById("message").value
    }).then(
      message => {
        if (message === "OK") {
          alert("Email sent successfully");
        } else {
          console.error("Error sending email:", message);
          alert("Error sending email. Check the console for details.");
        }
      }
    );
  }
</script>






        <!-- footer content -->
        <footer id="mainFooter" style="position: fixed; bottom: 0; left: 0; width: 85%">
            &copy; Copyright 2023 | Online Special Program for Employment of Student (SPES)
        </footer>

        <!-- /footer content -->
      </div>
    </div>

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

    $(document).ready(function () {
        // View Button Click Event
        $('.view-button').click(function () {
            // Get the applicant's information and perform the "View" action
            var row = $(this).closest('tr');
            var applicantNumber = row.find('td:eq(0)').text();
            var name = row.find('td:eq(1)').text();
            var email = row.find('td:eq(2)').text();
            var status = row.find('td:eq(3)').text();

            // You can implement the "View" action here, e.g., showing a modal with applicant details.
            // Replace the following alert with your custom code.
            alert('View clicked for Applicant Number: ' + applicantNumber + '\nName: ' + name + '\nEmail: ' + email + '\nStatus: ' + status);
        });

        // Approve Button Click Event
       // $('.approve-button').click(function () {
            // Get the applicant's information and perform the "Approve" action
            var row = $(this).closest('tr');
            var applicantNumber = row.find('td:eq(0)').text();
            
            // You can implement the "Approve" action here, e.g., updating the status to "Approved."
            // Replace the following alert with your custom code.
          //  alert('Approved clicked for Applicant Number: ' + applicantNumber);
       // });

        // Decline Button Click Event
        $('.decline-button').click(function () {
            // Get the applicant's information and perform the "Decline" action
            var row = $(this).closest('tr');
            var applicantNumber = row.find('td:eq(0)').text();
            
            // You can implement the "Decline" action here, e.g., updating the status to "Declined."
            // Replace the following alert with your custom code.
            alert('Decline clicked for Applicant Number: ' + applicantNumber);
        });
    });
</script>

<script>
    $(document).ready(function () {
        // Approve Button Click Event
        $('.approve-button').click(function () {
            var row = $(this).closest('tr');
            var applicantID = row.data('applicant-id');

            // Send an AJAX request to update the status to 'Approved'
            $.ajax({
                url: 'update_status.php', // Create a PHP script to handle the update
                method: 'POST',
                data: {
                    applicantID: applicantID,
                    newStatus: 'Approved'
                },
                success: function (response) {
                    // Check if the update was successful
                    if (response === 'success') {
                        // Update the status in the table
                        row.find('td:eq(4)').text('Approved');
                    } else {
                        alert('Failed to update status.');
                    }
                },
                error: function () {
                    alert('An error occurred while updating the status.');
                }
            });
        });

        // Decline Button Click Event
        $('.decline-button').click(function () {
            // Handle the decline action here (similar to the approve action).
            // You can send another AJAX request to update the status to 'Declined' if needed.
        });
    });
</script>

<script>
    $(document).ready(function () {
        // Decline Button Click Event
        $('.decline-button').click(function () {
            var row = $(this).closest('tr');
            var applicantID = row.data('applicant-id');

            // Send an AJAX request to update the status to 'Declined'
            $.ajax({
                url: 'update_status.php', // Create a PHP script to handle the update
                method: 'POST',
                data: {
                    applicantID: applicantID,
                    newStatus: 'Declined'
                },
                success: function (response) {
                    // Check if the update was successful
                    if (response === 'success') {
                        // Update the status in the table
                        row.find('td:eq(4)').text('Declined');
                    } else {
                        alert('Failed to update status.');
                    }
                },
                error: function () {
                    alert('An error occurred while updating the status.');
                }
            });
        });
    });
</script>

</body>
</html>