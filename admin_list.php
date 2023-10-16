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

$sql = "SELECT * FROM applicants";
$result = $conn->query($sql);

if (!$result) {
    die("Error in SQL query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> eSPES | Applicants' List </title>
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
                        <span>Welcome, <br>SPES Admin</br></span>
                        <h2> </h2>
                    </div>
                </div>
                <!-- /menu profile quick info -->
                <br />
                <!-- sidebar menu -->
                <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                    <div class="menu_section">
                        <h3>SPES Admin Menu</h3>
                        <ul class="nav side-menu">
                            <li><a href="http://localhost/Capstone/admin_homepage.php"><i class="fa fa-bars"></i>
                                    Applicants</a></li>
                            <li><a href="http://localhost/Capstone/admin_applicants.php"><i class="fa fa-bars"></i>
                                    Applicants' List</a></li>
                            <li><a href="http://localhost/Capstone/admin_list.php"><i class="fa fa-bars"></i>
                                    Approved Applicants</a></li>
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
			  <h2> SPES Admin </h2>
<br />
<br />

<!-- Box Container Rows with Table -->
<div class="box-container row box-b"> 
<?php if ($result->num_rows > 0) : ?>
        <table class="content-table">
        <thead>
                <tr>
                  <th>Applicant Number</th>
                  <th>Types of Application</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Status</th>
                  <th>Action</th>

                </tr>
            </thead>
            <tbody>
                <?php 
                while ($row = $result->fetch_assoc()) : ?>
                  <tr class="table-row">
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['type_Application'] ?></td>
                    <td><?= $row['first_Name'] .' '.$row['middle_Name'] .' '.$row['last_Name'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['status'] ?></td>
                
                    <td>
                    <a href="#details<?php echo $row['user_id']; ?>" data-toggle="modal" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-search"></span> View </a>
							    
<!-- Applicants Details -->

<div class="modal fade" id="details<?php echo $row['user_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center><h4 class="modal-title" id="myModalLabel">Applicants Full Details</h4></center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    </h5>
                    <table class="table table-bordered table-striped">
                    <thead>
                <tr>
                  <th>Applicant Number</th>
                  <th>Types of Application</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Status</th>

                </tr>
            </thead>
            <tbody>
              
            <?php 
            include("conn.php");
// Create a connection to the database
$conn = new mysqli($databaseHost, $databaseUsername, $databasePassword, $dbname);

// Query to select the row based on the provided 'id'
$sql="select * from applicant_documents where id='".$row['id']."'";
$query=$conn->query($sql);
$id = $row['id']; 
while($row=$query->fetch_array()){
					?>
					<tr>
						<td><?php echo $row['id']; ?></td>
						<td><?php echo "<img src='{$row['school_id_photo']}'; width='40%' height='40%'" ?></td>
                        <td><?php echo "<img src='{$row['birth_certificate']}'; width='40%' height='40%'" ?></td>
                        <td><a href="display_pdf.php?id=' . $row['id'] . '">View PDF</a></td>
                        <iframe src="' . $id . '" width="100%" height="500px"></iframe>
        
						</td>
					</tr>
					<?php
				}	?>
            </tbody>
    
                    </table>

                </div>
			</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Close</button>
            </div>
        </div>
     
    </div>
  
</div>


                    </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
<?php endif; ?>

</div>


        <!-- footer content -->
        <footer id="mainFooter">
          <div class="pull-right">
             &copy; Copyright 2023 | Online Special Program for Employment of Student (SPES) 
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>

    <!-- Custom Theme Scripts -->
    <script src="custom.js"></script>

</body>
</html>