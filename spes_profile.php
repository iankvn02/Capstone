<?php
// Start a PHP session (if not already started)
session_start();

// Database connection details
$databaseHost = 'localhost';
$databaseUsername = 'root';
$databasePassword = '';
$dbname = 'spes_db';

// Create a new MySQLi connection
$conn = new mysqli($databaseHost, $databaseUsername, $databasePassword, $dbname);

// Check for a successful connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["next"])) {
    // Validate form data
    $validationError = validateFormData($_POST);

    if (!$validationError) {
        // Get the user_id from the session if it exists
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        // Insert data into the database
        $inserted = insertApplicantData($conn, $_POST, $user_id);

        if ($inserted) {
            // Data inserted successfully, store it in the session
            $_SESSION['user_data'] = $_POST;

            // Redirect to pre_emp_doc.php
            header("Location: pre_emp_doc.php");
            exit;
        } else {
            echo '<script>alert("Registration failed. Please try again later.");</script>';
        }
    } else {
        echo '<script>alert("Validation error: ' . $validationError . '");</script>';
    }
}

// Function to validate form data
function validateFormData($formData) {
    // Implement your validation logic here
    // Return an error message as a string if validation fails, or return false if it passes
}
// Function to insert data into the database using prepared statements
function insertApplicantData($connection, $formData, $user_id) {
    // Assuming 'user_id' is a field in your 'applicants' table
    $fields = [
        'user_id', 'type_Application', 'first_Name', 'middle_Name', 'last_Name', 'birthday', 'place_of_birth', 'citizenship',
        'mobile_no', 'email', 'civil_status', 'sex', 'spes_type', 'parent_status', 'parents_displaced',
        'no_street', 'province_id', 'city_municipality_id', 'barangay_id', 'no_street2', 'province_id2',
        'city_municipality_id2', 'barangay_id2', 'father_first_name', 'father_middle_name',
        'father_last_name', 'father_contact_no', 'mother_first_name', 'mother_middle_name',
        'mother_last_name', 'mother_contact_no', 'elem_name', 'year_grade_level',
        'elem_date_attendance', 'hs_name', 'hs_degree', 'hs_year_level', 'hs_date_attendance',
        'suc_name', 'suc_course', 'suc_year_level', 'suc_date_attendance', 'status', 'spes_times'
    ];

    // Prepare the SQL statement with placeholders
    $placeholders = implode(', ', array_fill(0, count($fields), '?'));
    $sql = "INSERT INTO applicants (" . implode(", ", $fields) . ") VALUES ($placeholders)";

    $stmt = $connection->prepare($sql);

    // Check for SQL statement preparation errors
    if ($stmt === false) {
        return false;
    }

    // Create the types string for binding parameters
    $types = str_repeat("s", count($fields));

    // Bind parameters dynamically
    $bindParams = [&$types];
    if ($user_id !== null) {
        $bindParams[] = &$user_id;
    }
    foreach ($fields as $field) {
        if ($field !== 'user_id') { // Exclude 'user_id' field
            $bindParams[] = &$formData[$field];
        }
    }

    // Bind parameters to the prepared statement
    call_user_func_array([$stmt, 'bind_param'], $bindParams);

    // Execute the prepared statement
    $result = $stmt->execute();

    return $result;
}

// ...

// Data inserted successfully, store it in the session
$_SESSION['user_data'] = $_POST;

// Close the database connection
$conn->close();

?>





<!DOCTYPE html>
<html lang="en">
<head>
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

  <body class="nav-md" >
  <form id="demo-form2" class="form-horizontal form-label-left" method="POST" action=" " onsubmit="return validateForm();">
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
                 <li><a href="index.php"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                 </ul>
               </nav>
               </div> 
                     </div>
        <!-- /top navigation -->


        <div id="loader"></div>

        <!-- page content -->
        <div id="mainContent" class="right_col" role="main">
			
<div class="">
	<div class="page-title">
		<div class="alert alert-danger alert-dismissible fade in" role="alert" style="margin-top: 50px";>
			<b>The My Profile and Required Docs. section should be both 100%.</b>		</div>
	  <div class="title_left">
		<h3 style="font-size: 25px;">SPES Application Form</h3>
		</div>
		<div class="separator my-10"></div>
	</div>
	<div class="clearfix"></div>
	<div class="row">
	  <div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel">
		  <div class="x_title">
			<h2 style="font-size: 22px;"><small>Profile Details | Please fill out completely</small></h2>
			<ul class="nav navbar-right panel_toolbox">
			  <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
			  </li>
			  			  <li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
				<ul class="dropdown-menu" role="menu">
				  <li><a href="#">Settings 1</a>
				  </li>
				  <li><a href="#">Settings 2</a>
				  </li>
				</ul>
			  </li>
			  <li><a class="close-link"><i class="fa fa-close"></i></a>
			  </li>
			</ul>
			<div class="clearfix"></div>
		  </div>
		  <div class="x_content">
		  		
			<br /> 

			<form id="demo-form2" class="form-horizontal form-label-left" method="POST" action="">
		 	<div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12" for="type_Application">Type of Application:<span class="required">*</span></label>
				<div class="col-md-6 col-sm-6 col-xs-12">
				<input type="text" name="type_Application" id="type_Application" required="required" class="form-control col-md-7 col-xs-12" 
						value="<?php echo isset($_SESSION['user_data']['type_Application']) ? $_SESSION['user_data']['type_Application'] : ''; ?>" />
			</div>
			  </div>

			  <div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first_Name">First Name:<span class="required">*</span></label>
				<div class="col-md-6 col-sm-6 col-xs-12">
				<input type="text" name="first_Name" id="first_Name" required="required" class="form-control col-md-7 col-xs-12" 
						value="<?php echo isset($_SESSION['user_data']['first_Name']) ? $_SESSION['user_data']['first_Name'] : ''; ?>" />
			</div>
			  </div>

			  <div class="form-group">
				<label for="middle_Name" class="control-label col-md-3 col-sm-3 col-xs-12">Middle Name:</label>
				<div class="col-md-6 col-sm-6 col-xs-12">
				  <input id="middle_Name" class="form-control col-md-7 col-xs-12" type="text" required="required" name="middle_Name" value="" />
				</div>
			  </div>

			  <div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12" for="last_Name">Last Name:<span class="required">*</span></label>
				<div class="col-md-6 col-sm-6 col-xs-12">
				  <input type="text" id="last_Name" name="last_Name" required="required" class="form-control col-md-7 col-xs-12" required="required" value="" />
				</div>
			  </div>

			  <div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12">Date of Birth: <span class="required">*</span></label>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="form-control col-md-7 col-xs-12" required="required" type="date" name="birthday" id="birthday" placeholder="Date of Birth" value="" data-toggle="tooltip" data-placement="left" title="format: Month/Day/Year e.g. 02/21/2000" />
				</div>

				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="form-control col-md-7 col-xs-12" required="required" type="text" name="place_of_birth" id="Place of Birth" placeholder="Place of Birth" value="" />
				</div>

				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="form-control col-md-7 col-xs-12" required="required" type="text" name="citizenship" placeholder="Citizenship" value="" />
				</div>
			  </div>
			  			  <div class="ln_solid"></div>	
			  <div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12">Contact: <span class="required">*</span></label>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="form-control col-md-7 col-xs-12" required="required" type="text" name="mobile_no" placeholder="Mobile No." value="" />
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12">
				  <input class="form-control col-md-7 col-xs-12" type="text" required="required" id="email" name="email" placeholder="Email" value="" onblur="validate();"/>
				</div>
				<div class="col-md-2 col-sm-4 col-xs-12">				
					<p id="result"></p>
				</div>
			  </div>
			  <div class="ln_solid"></div>
			  		  	  			  			  			  		  			  
			  <div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12">Civil Status: *</label>
				<div class="col-md-6 col-sm-6 col-xs-12">
				  <div id="gender" class="btn-group" data-toggle="buttons">
					<div class="radio">
						<label><input type="radio" class="flat" name="civil_status" value="Single" required="required" /> Single </label>
					  </div>
					  <div class="radio">
						<label><input type="radio" class="flat" name="civil_status" value="Married" /> Married </label>
					  </div>
					  <div class="radio">
						<label><input type="radio" class="flat" name="civil_status" value="Widow/er" /> Widow/er </label>
					  </div>
					  <div class="radio">
						<label><input type="radio" class="flat" name="civil_status" value="Separated" /> Separated </label>
					  </div>
				  </div>
				</div>
			  </div>
			  
			  <div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12">Sex: *</label>
				<div class="col-md-6 col-sm-6 col-xs-12">
				  <div id="sex" class="btn-group" data-toggle="buttons">
					<div class="radio">
						<label><input type="radio" class="flat" name="sex" class="sex" value="Male" required="required"  /> Male </label>
					  </div>
					  <div class="radio">
						<label><input type="radio" class="flat" name="sex" class="sex" value="Female" /> Female </label>
					  </div>
				  </div>
				</div>
			  </div>
			  
			  <div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12">SPES Type: *</label>
				<div class="col-md-6 col-sm-6 col-xs-12">
				  <div id="gender" class="btn-group" data-toggle="buttons">
					<div class="radio">
						<label><input type="radio" class="flat" name="spes_type" value="Student" required="required"  /> Student </label>
					  </div>
					  <div class="radio">
						<label><input type="radio" class="flat" name="spes_type" value="ALS Student" /> ALS Student </label>
					  </div>
					  <div class="radio">
						<label><input type="radio" class="flat" name="spes_type" value="Out of School Youth" /> Out of School Youth (OSY) </label>
					  </div>
				  </div>
				</div>
			  </div>
			 
			  <div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12">Parent Status: *</label>
				<div class="col-md-6 col-sm-6 col-xs-12">
				  <div id="gender" class="btn-group" data-toggle="buttons">
					<div class="radio">
						<label><input type="radio" class="flat" name="parent_status" value="Living Together" required="required"  /> Living Together </label>
					  </div>
					  <div class="radio">
						<label><input type="radio" class="flat" name="parent_status" value="Solo Parent" /> Solo Parent </label>
					  </div>
					  <div class="radio">
						<label><input type="radio" class="flat" name="parent_status" value="Separated" /> Separated </label>
					  </div>
				  </div>
				</div>
			  </div>
			  
			  <div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12">Parent is displaced worker/s?: *</label>
				<div class="col-md-6 col-sm-6 col-xs-12">
				  <div id="gender" class="btn-group" data-toggle="buttons">
					  <div class="radio">
						<label><input type="radio" class="flat" name="parents_displaced" value="No" /> No </label>
					  </div>				  
					  <div class="radio">
						<label><input type="radio" class="flat" name="parents_displaced" value="Yes, Local" /> Yes, Local </label>
					  </div>
					  <div class="radio">
						<label><input type="radio" class="flat" name="parents_displaced" value="Yes, Abroad/OFW" /> Yes, Abroad/OFW </label>
					  </div>
				  </div>
				</div>
			  </div>
			  <div class="ln_solid"></div>			  
			  <div class="form-group">
			  	<label class="control-label col-md-3 col-sm-3 col-xs-12">Present Address St./Sitio: *</label>
				<div class="col-md-4 col-sm-3 col-xs-12">
				  <input class="form-control col-md-7 col-xs-12" type="text" name="no_street" placeholder="House No., Street/Sitio" value="" />
				</div>
			  </div>
			  <div class="form-group">
			  	<label class="control-label col-md-3 col-sm-3 col-xs-12">Province/City/Municipality/Barangay: *</label>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="form-control" type="text" name="province_id"  required="required" value="">
					<option value="0">Province</option>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="form-control" type="text" name="city_municipality_id"  required="required" value="">
				  		<option value=""> City/Municipality</option>				  </select>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="form-control" type="text" name="barangay_id"  required="required" value="">
				  		<option value=""> Barangay</option>				  </select>
				</div>
			  </div>
			  
			  <div class="form-group">
			  	<label class="control-label col-md-3 col-sm-3 col-xs-12">Permanent Address St./Sitio: *</label>
				<div class="col-md-4 col-sm-3 col-xs-12">
				  <input class="form-control col-md-7 col-xs-12" type="text" name="no_street2" placeholder="House No., Street/Sitio" value="" />
				</div>
			  </div>
			  <div class="form-group">
			  	<label class="control-label col-md-3 col-sm-3 col-xs-12">Province/City/Municipality/Barangay: *</label>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="form-control" type="text" name="province_id2"  required="required" value="">
					<option value=" "> Province</option>                                  
				  </select>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="form-control" type="text" name="city_municipality_id2"  required="required" value="">
				  		<option value=""> City/Municipality</option>				  </select>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="form-control" type="text" name="barangay_id2"  required="required" value="">
				  		<option value=""> Barangay</option>				  </select>
				</div>
			  </div>	
			  <div class="ln_solid"></div>
			  <div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12">Father's Name: <span class="required">*</span></label>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="date-picker form-control col-md-7 col-xs-12" required="required" type="text" name="father_first_name" placeholder="First Name" value="" />
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="form-control col-md-7 col-xs-12" required="required" type="text" name="father_middle_name" placeholder="Middle Name" value="" />
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="form-control col-md-7 col-xs-12" required="required" type="text" name="father_last_name" placeholder="Last Name" value="" />
				</div>
			  </div>
			  <div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12">Father's Contact No.: <span class="required">*</span></label>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="date-picker form-control col-md-7 col-xs-12" required="required" type="text" name="father_contact_no" placeholder="Mobile No." value=""/>
				</div>
			  </div>				  
			  <div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12">Mother's Name: <span class="required">*</span></label>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="date-picker form-control col-md-7 col-xs-12" required="required" type="text" name="mother_first_name" placeholder="First Name" value="" />
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="form-control col-md-7 col-xs-12" required="required" type="text" name="mother_middle_name" placeholder="Middle Name" value="" />
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="form-control col-md-7 col-xs-12" required="required" type="text" name="mother_last_name" placeholder="Last Name" value="" />
				</div>
			  </div>
			  <div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12">Mother's Contact No.: *</label>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="date-picker form-control col-md-7 col-xs-12" type="text" name="mother_contact_no" placeholder="Mobile No." value="" />
				</div>
			  </div>			  
			  <div class="ln_solid"></div>
			  			  <div class="form-group">
				<label class="control-label col-md-2 col-sm-2 col-xs-6">Elementary:<span class="required"> *</span></label>
				<div class="col-md-4 col-sm-2 col-xs-12">
				<input class="date-picker form-control col-md-7 col-xs-12" required="required" type="text" id="elem_name" name="elem_name" placeholder="Elementary School Name" value="" />
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="form-control col-md-7 col-xs-12" type="text" id="elem_degree" ame="elem_degree" placeholder="Degree" disabled />
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <select class="form-control" name="year_grade_level" id="year_grade_level" required="required">
					<option value="">Select Grade</option>
					<option value="Grade 1">Grade 1</option>
					<option value="Grade 2">Grade 2</option>
					<option value="Grade 3">Grade 3</option>
					<option value="Grade 4">Grade 4</option>
					<option value="Grade 5">Grade 5</option>
					<option value="Grade 6/Graduating">Grade 6/Graduating</option>
					<option value="Graduate">Graduate</option>					
				  </select>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="form-control col-md-7 col-xs-12" required="required" type="text" id="elem_date_attendance" name="elem_date_attendance" placeholder="Year Ended" data-toggle="tooltip" data-placement="left" value="" />
				</div>
			  </div>	
				<script>
					$('#year_grade_level').val();
				</script>
			  				
			  <div class="form-group">
				<label class="control-label col-md-2 col-sm-2 col-xs-6">High School: *</label>
				<div class="col-md-4 col-sm-2 col-xs-12">
				  <input class="date-picker form-control col-md-7 col-xs-12" required="required" type="text" id="hs_name" name="hs_name" placeholder="High School Name" value=""  />
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="form-control col-md-7 col-xs-12" type="text" id="hs_degree" name="hs_degree" placeholder="Degree, 'n/a' if None"  required="required" value="" />
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <select class="form-control" name="hs_year_level" id="hs_year_level" required="required">
				  	<option value="">Select Year</option>
					<option value="Grade 7/First Year">Grade 7/First Year</option>
					<option value="Grade 8/Second Year">Grade 8/Second Year</option>
					<option value="Grade 9/Third Year">Grade 9/Third Year</option>
					<option value="Grade 10/Fourth Year">Grade 10/Fourth Year</option>
					<option value="Grade 11/Senior High">Grade 11/Senior High 1</option>
					<option value="Grade 12/Senior High/Graduating">Grade 12/Senior High 2/Graduating</option>
					<option value="Graduate">Graduate</option>					
				  </select>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="form-control col-md-7 col-xs-12" required="required" type="text" id="hs_date_attendance" name="hs_date_attendance" placeholder="Year Ended" data-toggle="tooltip" data-placement="left" value="" />
				</div>
			  </div>
			  	<script>
					$('#hs_year_level').val();
				</script>
					
			  <div class="form-group">
				<label class="control-label col-md-2 col-sm-2 col-xs-6">College: </label>
				<div class="col-md-4 col-sm-2 col-xs-12">
				  <input class="date-picker form-control col-md-7 col-xs-12" type="text" id="suc_name" name="suc_name" placeholder="College Name (Leave as Blank if None)" value=""  />
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="form-control" name="suc_course" id="suc_course">
				  		
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <select class="form-control" name="suc_year_level" id="suc_year_level">
					<option value="">Select Year</option>
					<option value="First Year">First Year</option>
					<option value="Second Year">Second Year</option>
					<option value="Third Year">Third Year</option>
					<option value="Fourth Year">Fourth Year</option>					
					<option value="Fourth Year/Graduating">Fourth Year/Graduating</option>
					<option value="Fifth Year/Graduating">Fifth Year/Graduating</option>
					<option value="Graduate">Graduate</option>					
				  </select>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="form-control col-md-7 col-xs-12" type="text" id="suc_date_attendance" name="suc_date_attendance" placeholder="Year Ended" data-toggle="tooltip" data-placement="left" value="" />
				</div>
			  </div>	
			  
			  
			  	<script>
					$('#suc_year_level').val();
					$('#suc_course').val();
				</script>

					
			  </div>			
			  <div class="form-group">
			  	<label class="control-label col-md-3 col-sm-3 col-xs-12">How many times have you been a SPES beneficiary?:</label>
				<div class="col-md-3 col-sm-6 col-xs-12">
					<select class="form-control" id='spes_times' name="spes_times">
						<option name="spes_times" value="0" selected>0 (First Time)</option>
						<option name="spes_times" value="1">1</option>
						<option name="spes_times" value="2">2</option>
						<option name="spes_times" value="3">3</option>
						<option name="spes_times" value="4">4</option>
											</select>
					<br><br>
				</div>
					
			  </div>				
			  <div class="form-group">
            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                <button class="btn btn-primary" type="button" onclick="cancelEditProfile()">Cancel</button>
                <button class="btn btn-warning" type="reset">Reset</button>
				<button class="btn btn-success" type="submit" onclick="saveFormData()" name="next">Next</button>
            </div>
        </div>
    	</form>
		  </div>
		</div>
	  </div>
	</div>
	
	
<script>
  	function saveFormData() {
  const formData = {
    name: document.getElementById('name').value,
    // Add more fields as needed
  };

  // Store the form data in localStorage
  localStorage.setItem('formData', JSON.stringify(formData));

  // Redirect to the next page (pre_emp_doc.php)
  window.location.href = 'pre_emp_doc.php';
}
	  function validateForm() {
        var firstName = document.getElementById('first_Name').value;
        var lastName = document.getElementById('last_Name').value;
    
        console.log("First Name:", firstName);
        console.log("Last Name:", lastName);
    }

	
	function cancelEditProfile() {
		window.location.href = '../';
	}

	 document.getElementById("next").addEventListener("click", function() {
        window.location.href = "pre_emp_doc.php";
    });

	function validateEmail(email) {
	  var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	  return re.test(email);
	}

	function validate() {
	  var $result = $("#result");
	  var email = $("#email").val();
	  $result.text("");

	  if (validateEmail(email)) {
		$result.text(email + " is a valid Email.");
		$result.css("color", "green");
	  } else {
	  	$("#email").val('');
		$result.text(email + " is not a valid Email.");
		$result.css("color", "red");
	  }
	  return false;
	}
	</script>
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
   
<script>
        // JavaScript code here (place at the end of the HTML document)
        function cancelEditProfile() {
            window.location.href = 'index.php'; // Adjust the URL as needed
        }
    </script> 
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
</script>

	
  </body>
</html>