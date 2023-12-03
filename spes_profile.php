<?php
session_start();

$databaseHost = 'localhost';
$databaseUsername = 'root';
$databasePassword = '';
$dbname = "spes_db";

$conn = new mysqli($databaseHost, $databaseUsername, $databasePassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_SESSION['user_data'])) {
    $userData = $_SESSION['user_data'];
} else {
    $userData = array();
}

$first_Name = isset($_SESSION['user_data']['first_Name']) ? $_SESSION['user_data']['first_Name'] : '';
$middle_Name = isset($_SESSION['user_data']['middle_Name']) ? $_SESSION['user_data']['middle_Name'] : '';
$last_Name = isset($_SESSION['user_data']['last_Name']) ? $_SESSION['user_data']['last_Name'] : '';
$suffix = isset($_SESSION['user_data']['suffix']) ? $_SESSION['user_data']['suffix'] : '';
$birthday = isset($_SESSION['user_data']['birthday']) ? $_SESSION['user_data']['birthday'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["next"])) {
        $validationError = validateFormData($_POST);
        $_SESSION['user_data'] = $_POST;

        if (!$validationError) {
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            $inserted = insertApplicantData($conn, $_POST, $user_id);

            if ($inserted) {
                unset($_SESSION['user_data']);
                header("Location: pre_emp_doc.php");
                exit;
            } else {
                echo '<script>alert("Registration failed. Please try again later.");</script>';
            }
        } else {
            echo '<script>alert("Validation error: ' . $validationError . '");</script>';
        }
    } elseif (isset($_POST["update"])) {
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $updated = updateApplicantData($conn, $_POST, $user_id);

        if ($updated) {
            // Data updated successfully
            // Redirect or perform further actions
        } else {
            echo '<script>alert("Update failed. Please try again later.");</script>';
        }
    }
}

function validateFormData($formData) {
    // Implement your validation logic here
    // Return an error message as a string if validation fails, or return false if it passes
}

function insertApplicantData($connection, $formData, $user_id) {
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

    array_unshift($fields, 'user_id');
    $placeholders = implode(', ', array_fill(0, count($fields), '?'));

    $sql = "INSERT INTO applicants (" . implode(", ", $fields) . ") VALUES ($placeholders)";
    $stmt = $connection->prepare($sql);

    if ($stmt === false) {
        return false;
    }

    $types = str_repeat("s", count($fields));
    $bindParams = [&$types];

    if ($user_id !== null) {
        $bindParams[] = &$user_id;
    }

    foreach ($fields as $field) {
        if ($field !== 'user_id') {
            $bindParams[] = &$formData[$field];
        }
    }

    call_user_func_array([$stmt, 'bind_param'], $bindParams);
    $result = $stmt->execute();

    return $result;
}

function updateApplicantData($connection, $formData, $user_id) {
    $fields = [
        'type_Application', 'first_Name', 'middle_Name', 'last_Name', 'birthday', 'place_of_birth', 'citizenship',
        'mobile_no', 'email', 'civil_status', 'sex', 'spes_type', 'parent_status', 'parents_displaced',
        'no_street', 'province_id', 'city_municipality_id', 'barangay_id', 'no_street2', 'province_id2',
        'city_municipality_id2', 'barangay_id2', 'father_first_name', 'father_middle_name',
        'father_last_name', 'father_contact_no', 'mother_first_name', 'mother_middle_name',
        'mother_last_name', 'mother_contact_no', 'elem_name', 'year_grade_level',
        'elem_date_attendance', 'hs_name', 'hs_degree', 'hs_year_level', 'hs_date_attendance',
        'suc_name', 'suc_course', 'suc_year_level', 'suc_date_attendance', 'status', 'spes_times'
    ];
	$updateFields = [];
    $bindParams = ['']; // Initializing with an empty string for type definition
    
    foreach ($fields as $field) {
        if (isset($formData[$field])) {
            $updateFields[] = "$field = ?";
            $bindParams[0] .= 's'; // Appending 's' for string type
            $bindParams[] = &$formData[$field];
        }
    }

    $bindParams[0] .= 'i'; // Assuming 'user_id' is an integer
    $bindParams[] = &$user_id;

    $updateFieldsString = implode(", ", $updateFields);
    $sql = "UPDATE applicants SET $updateFieldsString WHERE user_id = ?";
    $stmt = $connection->prepare($sql);

    if ($stmt === false) {
        return false;
    }

    call_user_func_array([$stmt, 'bind_param'], $bindParams);
    $result = $stmt->execute();
		if ($result) {
			// Redirect to pre_emp_doc.php upon successful update
			header("Location: pre_emp_doc.php");
			exit;
		} else {
			// Handle update failure
			echo '<script>alert("Update failed. Please try again later.");</script>';
		}
    return $result;
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>eSPES | Applicant Home Page</title>
    <link href="bootstrap.css" rel="stylesheet">
    <link href="custom.css" rel="stylesheet">
	<link href="style.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
	
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">   
	<link rel="shortcut icon" type="x-icon" href="spes_logo.png">
	
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
				 <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
  				 <div class="menu_section">
					<a id="menu_toggle"><i class="#"></i></a>
						<h3>SPES Applicant Menu</h3>
						<ul class="nav side-menu">
						<li><a href="#" id="menu_toggle"><i class="#"></i> My Profile</a>
						<li><a href="#" id="menu_toggle"><i class="#"></i> Required Docs. </a>
						<li><a href="#" id="menu_toggle"><i class="#"></i> Submitted. </a>
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

				<div class="nav toggle">
					<a id="menu_toggle"><i class="fa fa-bars"></i></a>
				</div>

                 <ul class="nav navbar-nav navbar-right">
                 <li><a href="index.php"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                 </ul>
               </nav>
               </div> 
         </div>	
	
        <!-- /top navigation -->

        <div id="loader"></div>

        <!-- page content -->
        <div id="mainContent2" class="right_col" role="main">
		<nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item active" aria-current="page">My Profile	</li>
				
			</ol>
        </nav>
<div class="">
	<div class="page-title">
		<div class="alert alert-danger alert-dismissible fade in" role="alert" style="margin-top: 10px";>
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
			<br>
		  </div>
		  <div class="x_content">
		  		

			<form id="demo-form2" class="form-horizontal form-label-left" method="POST" action="process_profile.php">
			<div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12" for="type_Application">Type of Application:<span class="required">*</span></label>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<select name="type_Application" id="type_Application" required="required" class="form-control col-md-7 col-xs-12">
						<option value="">Select Type of Application</option>
						<option value="New Applicants" >New Applicants</option>
						<option value="Renewal">Renewal</option>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12" for="first_Name">First Name:<span class="required">*</span></label>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<input type="text" name="first_Name" id="first_Name" required="required" class="form-control col-md-7 col-xs-12" 
							 />
				</div>
			</div>

			<div class="form-group">
				<label for="middle_Name" class="control-label col-md-3 col-sm-3 col-xs-12">Middle Name:</label>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<input id="middle_Name" class="form-control col-md-7 col-xs-12" type="text" required="required" name="middle_Name" 
							pattern="[A-Za-z]+" title="Please enter only letters" 
							value="<?php echo isset($_SESSION['user_data']['middle_Name']) ? $_SESSION['user_data']['middle_Name'] : ''; ?>" 
							oninput="this.value = this.value.replace(/[^A-Za-z]/g, '')" />
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12" for="last_Name">Last Name:<span class="required">*</span></label>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<input type="text" id="last_Name" name="last_Name" required="required" class="form-control col-md-7 col-xs-12" required="required" 
						/>
				</div>
			</div>


			<div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12" for="suffix">Suffix:<span class="required">*</span></label>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<input type="text" id="suffix" name="suffix"  class="form-control col-md-7 col-xs-12"
						value="<?php echo isset($_SESSION['user_data']['suffix']) ? $_SESSION['user_data']['suffix'] : ''; ?>" />
				</div>
			</div>

			  <div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12">Date of Birth: <span class="required">*</span></label>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="form-control col-md-7 col-xs-12" required="required" type="date" name="birthday" id="birthday" placeholder="Date of Birth" 
				  value="<?php echo isset($_SESSION['user_data']['birthday']) ? $_SESSION['user_data']['birthday'] : ''; ?>"
				   data-toggle="tooltip" data-placement="left" title="format: Month/Day/Year e.g. 02/21/2000" />
				</div>

				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="form-control col-md-7 col-xs-12" required="required" type="text" name="place_of_birth" id="place_of_birth" placeholder="Place of Birth"
				  value="<?php echo isset($_SESSION['user_data']['place_of_birth']) ? $_SESSION['user_data']['place_of_birth'] : ''; ?>" />
				</div>

				<div class="col-md-2 col-sm-2 col-xs-12">
				<select class="form-control" name="citizenship" id="citizenship" required="required">
					<option value="">Select Citizenship</option>
                    <option value="Filipino">Filipino</option>
				  </select>	
				</div>
			  	</div>

			  	<div class="ln_solid"></div>
					<div class="form-group">
						<label class="control-label col-md-3 col-sm-3 col-xs-12">Contact: <span class="required">*</span></label>
						<div class="col-md-2 col-sm-2 col-xs-12">
							<input class="form-control col-md-7 col-xs-12" required="required" type="text" name="mobile_no" id="mobile_no" placeholder="Mobile No." 
								pattern="[0-9]{11}" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 11)"
								value="<?php echo isset($_SESSION['user_data']['mobile_no']) ? $_SESSION['user_data']['mobile_no'] : ''; ?>" />
						</div>

						<div class="col-md-4 col-sm-4 col-xs-12">
							<input class="form-control col-md-7 col-xs-12" type="text" required="required" id="email" name="email" placeholder="Email" 
								value="<?php echo isset($_SESSION['user_data']['email']) ? $_SESSION['user_data']['email'] : ''; ?>" onblur="validate();" />
						</div>
						<div class="col-md-2 col-sm-4 col-xs-12">
							<p id="result"></p>
						</div>
					</div>

			  <div class="ln_solid"></div>
			  		  	  			  			  			  		  			  
			  <div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12">Civil Status: *</label>
				<div class="col-md-6 col-sm-6 col-xs-12">
				  <div id="civil_status" class="btn-group" data-toggle="buttons">
				  <div class="radio">
                <label>
                    <input type="radio" class="flat" name="civil_status" id="civil_status"value="Single" required="required"
                           <?php echo isset($_SESSION['user_data']['civil_status']) && $_SESSION['user_data']['civil_status'] === 'Single' ? 'checked' : ''; ?> />
                    Single
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" class="flat" name="civil_status" id="civil_status" value="Married"
                           <?php echo isset($_SESSION['user_data']['civil_status']) && $_SESSION['user_data']['civil_status'] === 'Married' ? 'checked' : ''; ?> />
                    Married
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" class="flat" name="civil_status" id="civil_status" value="Widow/er"
                           <?php echo isset($_SESSION['user_data']['civil_status']) && $_SESSION['user_data']['civil_status'] === 'Widow/er' ? 'checked' : ''; ?> />
                    Widow/er
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" class="flat" name="civil_status" id="civil_status" value="Separated"
                           <?php echo isset($_SESSION['user_data']['civil_status']) && $_SESSION['user_data']['civil_status'] === 'Separated' ? 'checked' : ''; ?> />
                    Separated
                </label>
					  </div>
				  </div>
				</div>
			  </div>
			  
			  <div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12">Sex: *</label>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div id="sex" class="btn-group" data-toggle="buttons">
						<div class="radio">
							<label>
								<input type="radio" class="flat" name="sex" class="sex" value="Male" required="required" 
								<?php echo isset($_SESSION['user_data']['sex']) && $_SESSION['user_data']['sex'] === 'Male' ? 'checked' : ''; ?> /> Male
							</label>
						</div>
						<div class="radio">
							<label>
								<input type="radio" class="flat" name="sex" class="sex" value="Female"
								<?php echo isset($_SESSION['user_data']['sex']) && $_SESSION['user_data']['sex'] === 'Female' ? 'checked' : ''; ?> /> Female
							</label>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12">SPES Type: *</label>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div id="gender" class="btn-group" data-toggle="buttons">
						<div class="radio">
							<label>
								<input type="radio" class="flat" name="spes_type" value="Student" required="required"
								<?php echo isset($_SESSION['user_data']['spes_type']) && $_SESSION['user_data']['spes_type'] === 'Student' ? 'checked' : ''; ?> /> Student
							</label>
						</div>
						<div class="radio">
							<label>
								<input type="radio" class="flat" name="spes_type" value="ALS Student"
								<?php echo isset($_SESSION['user_data']['spes_type']) && $_SESSION['user_data']['spes_type'] === 'ALS Student' ? 'checked' : ''; ?> /> ALS Student
							</label>
						</div>
						<div class="radio">
							<label>
								<input type="radio" class="flat" name="spes_type" value="Out of School Youth"
								<?php echo isset($_SESSION['user_data']['spes_type']) && $_SESSION['user_data']['spes_type'] === 'Out of School Youth' ? 'checked' : ''; ?> /> Out of School Youth (OSY)
							</label>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12">Parent Status: *</label>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div id="gender" class="btn-group" data-toggle="buttons">
						<div class="radio">
							<label>
								<input type="radio" class="flat" name="parent_status" value="Living Tog" required="required"
								<?php echo isset($_SESSION['user_data']['parent_status']) && $_SESSION['user_data']['parent_status'] === 'Living Together' ? 'checked' : ''; ?> /> Living Together
							</label>
						</div>
						<div class="radio">
							<label>
								<input type="radio" class="flat" name="parent_status" value="Solo Parent"
								<?php echo isset($_SESSION['user_data']['parent_status']) && $_SESSION['user_data']['parent_status'] === 'Solo Parent' ? 'checked' : ''; ?> /> Solo Parent
							</label>
						</div>
						<div class="radio">
							<label>
								<input type="radio" class="flat" name="parent_status" value="Separated"
								<?php echo isset($_SESSION['user_data']['parent_status']) && $_SESSION['user_data']['parent_status'] === 'Separated' ? 'checked' : ''; ?> /> Separated
							</label>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12">Parent is displaced worker/s?: *</label>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div id="gender" class="btn-group" data-toggle="buttons">
						<div class="radio">
							<label>
								<input type="radio" class="flat" name="parents_displaced" value="No"
								<?php echo isset($_SESSION['user_data']['parents_displaced']) && $_SESSION['user_data']['parents_displaced'] === 'No' ? 'checked' : ''; ?> /> No
							</label>
						</div>
						<div class="radio">
							<label>
								<input type="radio" class="flat" name="parents_displaced" value="Yes, Local"
								<?php echo isset($_SESSION['user_data']['parents_displaced']) && $_SESSION['user_data']['parents_displaced'] === 'Yes, Local' ? 'checked' : ''; ?> /> Yes, Local
							</label>
						</div>
						<div class="radio">
							<label>
								<input type="radio" class="flat" name="parents_displaced" value="Yes, Abroad/OFW"
								<?php echo isset($_SESSION['user_data']['parents_displaced']) && $_SESSION['user_data']['parents_displaced'] === 'Yes, Abroad/OFW' ? 'checked' : ''; ?> /> Yes, Abroad/OFW
							</label>
						</div>
					</div>
				</div>
			</div>

			  <div class="ln_solid"></div>			  
			  <div class="form-group">
			  	<label class="control-label col-md-3 col-sm-3 col-xs-12">Present Address St./Sitio: *</label>
				<div class="col-md-4 col-sm-3 col-xs-12">
				  <input class="form-control col-md-7 col-xs-12" type="text" name="no_street"id="no_street" placeholder="House No., Street/Sitio" 
				  value="<?php echo isset($_SESSION['user_data']['no_street']) ? $_SESSION['user_data']['no_street'] : ''; ?>" />
				</div>
			  </div>
			  <div class="form-group">
			  	<label class="control-label col-md-3 col-sm-3 col-xs-12">Province/City/Municipality/Barangay: *</label>
				  <div class="col-md-2 col-sm-2 col-xs-12">
				  <select class="form-control" name="province_id" id="province_id" required="required">
					<option value="">Select Province</option>
                    <option value="Batangas">Batangas</option>
				  </select>
				</div>

				<div class="col-md-2 col-sm-2 col-xs-12">
					<select class="form-control" name="city_municipality_id" id="city_municipality_id" required="required">
				  		<option value="">Select City/Municipality</option>	
						<option value="Batangas City">Batangas City</option>			  
					</select>
				  
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
				<select class="form-control" name="barangay_id" id="barangay_id" required="required">
				<option value="">Select a Barangay</option>
				<option value="Alangilan">Alangilan</option>
				<option value="Balagtas">Balagtas</option>
				<option value="Balete">Balete</option>
				<option value="Banaba Center">Banaba Center</option>
				<option value="Banaba Ibaba">Banaba Ibaba</option>
				<option value="Banaba Kanluran">Banaba Kanluran</option>
				<option value="Banaba Silangan">Banaba Silangan</option>
				<option value="Barangay 1 (Pob.)">Barangay 1 (Pob.)</option>
				<option value="Barangay 10 (Pob.)">Barangay 10 (Pob.)</option>
				<option value="Barangay 11 (Pob.)">Barangay 11 (Pob.)</option>
				<option value="Barangay 12 (Pob.)">Barangay 12 (Pob.)</option>
				<option value="Barangay 13 (Pob.)">Barangay 13 (Pob.)</option>
				<option value="Barangay 14 (Pob.)">Barangay 14 (Pob.)</option>
				<option value="Barangay 15 (Pob.)">Barangay 15 (Pob.)</option>
				<option value="Barangay 16 (Pob.)">Barangay 16 (Pob.)</option>
				<option value="Barangay 17 (Pob.)">Barangay 17 (Pob.)</option>
				<option value="Barangay 18 (Pob.)">Barangay 18 (Pob.)</option>
				<option value="Barangay 19 (Pob.)">Barangay 19 (Pob.)</option>
				<option value="Barangay 2 (Pob.)">Barangay 2 (Pob.)</option>
				<option value="Barangay 20 (Pob.)">Barangay 20 (Pob.)</option>
				<option value="Barangay 21 (Pob.)">Barangay 21 (Pob.)</option>
				<option value="Barangay 22 (Pob.)">Barangay 22 (Pob.)</option>
				<option value="Barangay 23 (Pob.)">Barangay 23 (Pob.)</option>
				<option value="Barangay 24 (Pob.)">Barangay 24 (Pob.)</option>
				<option value="Barangay 3 (Pob.)">Barangay 3 (Pob.)</option>
				<option value="Barangay 4 (Pob.)">Barangay 4 (Pob.)</option>
				<option value="Barangay 5 (Pob.)">Barangay 5 (Pob.)</option>
				<option value="Barangay 6 (Pob.)">Barangay 6 (Pob.)</option>
				<option value="Barangay 7 (Pob.)">Barangay 7 (Pob.)</option>
				<option value="Barangay 8 (Pob.)">Barangay 8 (Pob.)</option>
				<option value="Barangay 9 (Pob.)">Barangay 9 (Pob.)</option>
				<option value="Bilogo">Bilogo</option>
				<option value="Bolbok">Bolbok</option>
				<option value="Bukal">Bukal</option>
				<option value="Calicanto">Calicanto</option>
				<option value="Catandala">Catandala</option>
				<option value="Concepcion">Concepcion</option>
				<option value="Conde Itaas">Conde Itaas</option>
				<option value="Conde Labak">Conde Labak</option>
				<option value="Cuta">Cuta</option>
				<option value="Dalig">Dalig</option>
				<option value="Dela Paz">Dela Paz</option>
				<option value="Dela Paz Pulot Aplaya">Dela Paz Pulot Aplaya</option>
				<option value="Dela Paz Pulot Itaas">Dela Paz Pulot Itaas</option>
				<option value="Domoclay">Domoclay</option>
				<option value="Dumantay">Dumantay</option>
				<option value="Gulod Itaas">Gulod Itaas</option>
				<option value="Gulod Labak">Gulod Labak</option>
				<option value="Haligue Kanluran">Haligue Kanluran</option>
				<option value="Haligue Silangan">Haligue Silangan</option>
				<option value="Ilihan">Ilihan</option>
				<option value="Kumba">Kumba</option>
				<option value="Kumintang Ibaba">Kumintang Ibaba</option>
				<option value="Kumintang Ilaya">Kumintang Ilaya</option>
				<option value="Libjo">Libjo</option>
				<option value="Liponpon, Isla Verde">Liponpon, Isla Verde</option>
				<option value="Maapas">Maapas</option>
				<option value="Mabacong">Mabacong</option>
				<option value="Mahabang Dahilig">Mahabang Dahilig</option>
				<option value="Mahabang Parang">Mahabang Parang</option>
				<option value="Mahacot Kanluran">Mahacot Kanluran</option>
				<option value="Mahacot Silangan">Mahacot Silangan</option>
				<option value="Malalim">Malalim</option>
				<option value="Malibayo">Malibayo</option>
				<option value="Malitam">Malitam</option>
				<option value="Maruclap">Maruclap</option>
				<option value="Pagkilatan">Pagkilatan</option>
				<option value="Paharang Kanluran">Paharang Kanluran</option>
				<option value="Paharang Silangan">Paharang Silangan</option>
				<option value="Pallocan Kanluran">Pallocan Kanluran</option>
				<option value="Pallocan Silangan">Pallocan Silangan</option>
				<option value="Pinamucan">Pinamucan</option>
				<option value="Pinamucan Ibaba">Pinamucan Ibaba</option>
				<option value="Pinamucan Silangan">Pinamucan Silangan</option>
				<option value="Sampaga">Sampaga</option>
				<option value="San Agapito, Isla Verde">San Agapito, Isla Verde</option>
				<option value="San Agustin Kanluran, Isla Verde">San Agustin Kanluran, Isla Verde</option>
				<option value="San Agustin Silangan, Isla Verde">San Agustin Silangan, Isla Verde</option>
				<option value="San Andres, Isla Verde">San Andres, Isla Verde</option>
				<option value="San Antonio, Isla Verde">San Antonio, Isla Verde</option>
				<option value="San Isidro">San Isidro</option>
				<option value="San Jose Sico">San Jose Sico</option>
				<option value="San Miguel">San Miguel</option>
				<option value="San Pedro">San Pedro</option>
				<option value="Santa Clara">Santa Clara</option>
				<option value="Santa Rita Aplaya">Santa Rita Aplaya</option>
				<option value="Santa Rita Karsada">Santa Rita Karsada</option>
				<option value="Santo Domingo">Santo Domingo</option>
				<option value="Santo Niño">Santo Niño</option>
				<option value="Simlong">Simlong</option>
				<option value="Sirang Lupa">Sirang Lupa</option>
				<option value="Sorosoro Ibaba">Sorosoro Ibaba</option>
				<option value="Sorosoro Ilaya">Sorosoro Ilaya</option>
				<option value="Sorosoro Karsada">Sorosoro Karsada</option>
				<option value="Tabangao Ambulong">Tabangao Ambulong</option>
				<option value="Tabangao Aplaya">Tabangao Aplaya</option>
				<option value="Tabangao Dao">Tabangao Dao</option>
				<option value="Talahib Pandayan">Talahib Pandayan</option>
				<option value="Talahib Payapa">Talahib Payapa</option>
				<option value="Talumpok Kanluran">Talumpok Kanluran</option>
				<option value="Talumpok Silangan">Talumpok Silangan</option>
				<option value="Tinga Itaas">Tinga Itaas</option>
				<option value="Tinga Labak">Tinga Labak</option>
				<option value="Tulo">Tulo</option>
				<option value="Wawa">Wawa</option>		  
				</select>
				  
				</div>
			  </div>
			  
			  <div class="form-group">
			  	<label class="control-label col-md-3 col-sm-3 col-xs-12">Permanent Address St./Sitio: *</label>
				<div class="col-md-4 col-sm-3 col-xs-12">
				  <input class="form-control col-md-7 col-xs-12" type="text" name="no_street2" id="no_street2" placeholder="House No., Street/Sitio" 
				  value="<?php echo isset($_SESSION['user_data']['no_street2']) ? $_SESSION['user_data']['no_street2'] : ''; ?>" />
				</div>
			  </div>
			  <div class="form-group">
			  	<label class="control-label col-md-3 col-sm-3 col-xs-12">Province/City/Municipality/Barangay: *</label>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <select class="form-control" name="province_id2" id="province_id2" required="required">
					<option value="">Select Province</option>
                    <option value="Batangas">Batangas</option>
				  </select>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
				<select class="form-control" name="city_municipality_id2" id="city_municipality_id2" required="required">
				  		<option value="">Select City/Municipality</option>	
						<option value="Batangas City">Batangas City</option>			  
					</select>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
				<select class="form-control" name="barangay_id2" id="barangay_id2" required="required">
				<option value="">Select a Barangay</option>
				<option value="Alangilan">Alangilan</option>
				<option value="Balagtas">Balagtas</option>
				<option value="Balete">Balete</option>
				<option value="Banaba Center">Banaba Center</option>
				<option value="Banaba Ibaba">Banaba Ibaba</option>
				<option value="Banaba Kanluran">Banaba Kanluran</option>
				<option value="Banaba Silangan">Banaba Silangan</option>
				<option value="Barangay 1 (Pob.)">Barangay 1 (Pob.)</option>
				<option value="Barangay 10 (Pob.)">Barangay 10 (Pob.)</option>
				<option value="Barangay 11 (Pob.)">Barangay 11 (Pob.)</option>
				<option value="Barangay 12 (Pob.)">Barangay 12 (Pob.)</option>
				<option value="Barangay 13 (Pob.)">Barangay 13 (Pob.)</option>
				<option value="Barangay 14 (Pob.)">Barangay 14 (Pob.)</option>
				<option value="Barangay 15 (Pob.)">Barangay 15 (Pob.)</option>
				<option value="Barangay 16 (Pob.)">Barangay 16 (Pob.)</option>
				<option value="Barangay 17 (Pob.)">Barangay 17 (Pob.)</option>
				<option value="Barangay 18 (Pob.)">Barangay 18 (Pob.)</option>
				<option value="Barangay 19 (Pob.)">Barangay 19 (Pob.)</option>
				<option value="Barangay 2 (Pob.)">Barangay 2 (Pob.)</option>
				<option value="Barangay 20 (Pob.)">Barangay 20 (Pob.)</option>
				<option value="Barangay 21 (Pob.)">Barangay 21 (Pob.)</option>
				<option value="Barangay 22 (Pob.)">Barangay 22 (Pob.)</option>
				<option value="Barangay 23 (Pob.)">Barangay 23 (Pob.)</option>
				<option value="Barangay 24 (Pob.)">Barangay 24 (Pob.)</option>
				<option value="Barangay 3 (Pob.)">Barangay 3 (Pob.)</option>
				<option value="Barangay 4 (Pob.)">Barangay 4 (Pob.)</option>
				<option value="Barangay 5 (Pob.)">Barangay 5 (Pob.)</option>
				<option value="Barangay 6 (Pob.)">Barangay 6 (Pob.)</option>
				<option value="Barangay 7 (Pob.)">Barangay 7 (Pob.)</option>
				<option value="Barangay 8 (Pob.)">Barangay 8 (Pob.)</option>
				<option value="Barangay 9 (Pob.)">Barangay 9 (Pob.)</option>
				<option value="Bilogo">Bilogo</option>
				<option value="Bolbok">Bolbok</option>
				<option value="Bukal">Bukal</option>
				<option value="Calicanto">Calicanto</option>
				<option value="Catandala">Catandala</option>
				<option value="Concepcion">Concepcion</option>
				<option value="Conde Itaas">Conde Itaas</option>
				<option value="Conde Labak">Conde Labak</option>
				<option value="Cuta">Cuta</option>
				<option value="Dalig">Dalig</option>
				<option value="Dela Paz">Dela Paz</option>
				<option value="Dela Paz Pulot Aplaya">Dela Paz Pulot Aplaya</option>
				<option value="Dela Paz Pulot Itaas">Dela Paz Pulot Itaas</option>
				<option value="Domoclay">Domoclay</option>
				<option value="Dumantay">Dumantay</option>
				<option value="Gulod Itaas">Gulod Itaas</option>
				<option value="Gulod Labak">Gulod Labak</option>
				<option value="Haligue Kanluran">Haligue Kanluran</option>
				<option value="Haligue Silangan">Haligue Silangan</option>
				<option value="Ilihan">Ilihan</option>
				<option value="Kumba">Kumba</option>
				<option value="Kumintang Ibaba">Kumintang Ibaba</option>
				<option value="Kumintang Ilaya">Kumintang Ilaya</option>
				<option value="Libjo">Libjo</option>
				<option value="Liponpon, Isla Verde">Liponpon, Isla Verde</option>
				<option value="Maapas">Maapas</option>
				<option value="Mabacong">Mabacong</option>
				<option value="Mahabang Dahilig">Mahabang Dahilig</option>
				<option value="Mahabang Parang">Mahabang Parang</option>
				<option value="Mahacot Kanluran">Mahacot Kanluran</option>
				<option value="Mahacot Silangan">Mahacot Silangan</option>
				<option value="Malalim">Malalim</option>
				<option value="Malibayo">Malibayo</option>
				<option value="Malitam">Malitam</option>
				<option value="Maruclap">Maruclap</option>
				<option value="Pagkilatan">Pagkilatan</option>
				<option value="Paharang Kanluran">Paharang Kanluran</option>
				<option value="Paharang Silangan">Paharang Silangan</option>
				<option value="Pallocan Kanluran">Pallocan Kanluran</option>
				<option value="Pallocan Silangan">Pallocan Silangan</option>
				<option value="Pinamucan">Pinamucan</option>
				<option value="Pinamucan Ibaba">Pinamucan Ibaba</option>
				<option value="Pinamucan Silangan">Pinamucan Silangan</option>
				<option value="Sampaga">Sampaga</option>
				<option value="San Agapito, Isla Verde">San Agapito, Isla Verde</option>
				<option value="San Agustin Kanluran, Isla Verde">San Agustin Kanluran, Isla Verde</option>
				<option value="San Agustin Silangan, Isla Verde">San Agustin Silangan, Isla Verde</option>
				<option value="San Andres, Isla Verde">San Andres, Isla Verde</option>
				<option value="San Antonio, Isla Verde">San Antonio, Isla Verde</option>
				<option value="San Isidro">San Isidro</option>
				<option value="San Jose Sico">San Jose Sico</option>
				<option value="San Miguel">San Miguel</option>
				<option value="San Pedro">San Pedro</option>
				<option value="Santa Clara">Santa Clara</option>
				<option value="Santa Rita Aplaya">Santa Rita Aplaya</option>
				<option value="Santa Rita Karsada">Santa Rita Karsada</option>
				<option value="Santo Domingo">Santo Domingo</option>
				<option value="Santo Niño">Santo Niño</option>
				<option value="Simlong">Simlong</option>
				<option value="Sirang Lupa">Sirang Lupa</option>
				<option value="Sorosoro Ibaba">Sorosoro Ibaba</option>
				<option value="Sorosoro Ilaya">Sorosoro Ilaya</option>
				<option value="Sorosoro Karsada">Sorosoro Karsada</option>
				<option value="Tabangao Ambulong">Tabangao Ambulong</option>
				<option value="Tabangao Aplaya">Tabangao Aplaya</option>
				<option value="Tabangao Dao">Tabangao Dao</option>
				<option value="Talahib Pandayan">Talahib Pandayan</option>
				<option value="Talahib Payapa">Talahib Payapa</option>
				<option value="Talumpok Kanluran">Talumpok Kanluran</option>
				<option value="Talumpok Silangan">Talumpok Silangan</option>
				<option value="Tinga Itaas">Tinga Itaas</option>
				<option value="Tinga Labak">Tinga Labak</option>
				<option value="Tulo">Tulo</option>
				<option value="Wawa">Wawa</option>		  
				</select>
				</div>
			  </div>	
			  
			  <div class="ln_solid"></div>
			  <div class="form-group">
				<label class="control-label col-md-3 col-sm-3 col-xs-12">Father's Name: <span class="required">*</span></label>
				<div class="col-md-2 col-sm-2 col-xs-12">
					<input class="date-picker form-control col-md-7 col-xs-12" required="required" type="text" name="father_first_name" id="father_first_name" placeholder="First Name" 
						pattern="[A-Za-z]+" title="Please enter only letters" 
						value="<?php echo isset($_SESSION['user_data']['father_first_name']) ? $_SESSION['user_data']['father_first_name'] : ''; ?>" 
						oninput="this.value = this.value.replace(/[^A-Za-z]/g, '')" />
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
					<input class="form-control col-md-7 col-xs-12" required="required" type="text"  name="father_middle_name" id="father_middle_name" placeholder="Middle Name" 
						pattern="[A-Za-z]+" title="Please enter only letters" 
						value="<?php echo isset($_SESSION['user_data']['father_middle_name']) ? $_SESSION['user_data']['father_middle_name'] : ''; ?>" 
						oninput="this.value = this.value.replace(/[^A-Za-z]/g, '')" />
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
					<input class="form-control col-md-7 col-xs-12" required="required" type="text" id="father_last_name" name="father_last_name" placeholder="Last Name" 
						pattern="[A-Za-z]+" title="Please enter only letters" 
						value="<?php echo isset($_SESSION['user_data']['father_last_name']) ? $_SESSION['user_data']['father_last_name'] : ''; ?>" 
						oninput="this.value = this.value.replace(/[^A-Za-z]/g, '')" />
				</div>
			
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="form-control col-md-7 col-xs-12" type="text" id="father_suffix"name="father_suffix" placeholder="Suffix" 
				  value="" />
				</div>
			  </div>
			  <div class="form-group">
					<label class="control-label col-md-3 col-sm-3 col-xs-12">Father's Contact No.: <span class="required">*</span></label>
					<div class="col-md-2 col-sm-2 col-xs-12">
						<input class="date-picker form-control col-md-7 col-xs-12" required="required" type="text" name="father_contact_no" id="father_contact_no" placeholder="Mobile No." 
							pattern="[0-9]{11}" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 11)"
							value="<?php echo isset($_SESSION['user_data']['father_contact_no']) ? $_SESSION['user_data']['father_contact_no'] : ''; ?>" />
					</div>
				</div>
			  
				<div class="form-group">
					<label class="control-label col-md-3 col-sm-3 col-xs-12">Mother's Maiden Name: <span class="required">*</span></label>
					<div class="col-md-2 col-sm-2 col-xs-12">
						<input class="date-picker form-control col-md-7 col-xs-12" required="required" type="text" id="mother_first_name"  name="mother_first_name" placeholder="First Name" 
							pattern="[A-Za-z]+" title="Please enter only letters" 
							value="<?php echo isset($_SESSION['user_data']['mother_first_name']) ? $_SESSION['user_data']['mother_first_name'] : ''; ?>" 
							oninput="this.value = this.value.replace(/[^A-Za-z]/g, '')" />
					</div>

					<div class="col-md-2 col-sm-2 col-xs-12">
						<input class="form-control col-md-7 col-xs-12" required="required" type="text" name="mother_middle_name"  id="mother_middle_name" placeholder="Middle Name" 
							pattern="[A-Za-z]+" title="Please enter only letters" 
							value="<?php echo isset($_SESSION['user_data']['mother_middle_name']) ? $_SESSION['user_data']['mother_middle_name'] : ''; ?>" 
							oninput="this.value = this.value.replace(/[^A-Za-z]/g, '')" />
					</div>

					<div class="col-md-2 col-sm-2 col-xs-12">
						<input class="form-control col-md-7 col-xs-12" required="required" type="text" id="mother_last_name" name="mother_last_name" placeholder="Last Name" 
							pattern="[A-Za-z]+" title="Please enter only letters" 
							value="<?php echo isset($_SESSION['user_data']['mother_last_name']) ? $_SESSION['user_data']['mother_last_name'] : ''; ?>" 
							oninput="this.value = this.value.replace(/[^A-Za-z]/g, '')" />
					</div>
				</div>


			  <div class="form-group">
					<label class="control-label col-md-3 col-sm-3 col-xs-12">Mother's Contact No.: <span class="required">*</span></label>
					<div class="col-md-2 col-sm-2 col-xs-12">
						<input class="date-picker form-control col-md-7 col-xs-12" required="required" type="text" id="mother_contact_no" name="mother_contact_no" placeholder="Mobile No." 
							pattern="[0-9]{11}" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 11)"
							value="<?php echo isset($_SESSION['user_data']['mother_contact_no']) ? $_SESSION['user_data']['mother_contact_no'] : ''; ?>" />
					</div>
				</div>
			  
			  <div class="ln_solid"></div>
			  			  <div class="form-group">
				<label class="control-label col-md-2 col-sm-2 col-xs-6">Elementary:<span class="required"> *</span></label>
				<div class="col-md-4 col-sm-2 col-xs-12">
				<input class="date-picker form-control col-md-7 col-xs-12" required="required" type="text" id="elem_name" name="elem_name" placeholder="Elementary School Name" 
				value="<?php echo isset($_SESSION['user_data']['elem_name']) ? $_SESSION['user_data']['elem_name'] : ''; ?>" />
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
					<select class="form-control col-md-7 col-xs-12" id="elem_degree" name="elem_degree">
						<option value="">Select Degree</option>
						<option value="Achiever">Achiever</option>
						<option value="With Honors">With Honors</option>
						<option value="With High Honors">With High Honors</option>
						<option value="With Highest Honors">With Highest Honors</option>
						<option value="None">None</option>
					</select>
				</div>

				<div class="col-md-2 col-sm-2 col-xs-12">
				<select class="form-control col-md-7 col-xs-12" id="year_grade_level" name="year_grade_level" required="required" >
						<option value="Graduate">Graduate</option>
					</select>
					<input type="hidden" name="year_grade_level_hidden" value="Graduate">
				</div>

				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="form-control col-md-7 col-xs-12" required="required" type="text" id="elem_date_attendance" name="elem_date_attendance" placeholder="Year Ended" data-toggle="tooltip" data-placement="left" 
				  value="<?php echo isset($_SESSION['user_data']['elem_date_attendance']) ? $_SESSION['user_data']['elem_date_attendance'] : ''; ?>" />
				</div>
			  </div>	
				<script>
					$('#year_grade_level').val();
				</script>
			  				
			  <div class="form-group">
				<label class="control-label col-md-2 col-sm-2 col-xs-6">High School: *</label>
				<div class="col-md-4 col-sm-2 col-xs-12">
				  <input class="date-picker form-control col-md-7 col-xs-12" required="required" type="text" id="hs_name" name="hs_name" placeholder="High School Name" 
				  value="<?php echo isset($_SESSION['user_data']['hs_name']) ? $_SESSION['user_data']['hs_name'] : ''; ?>" />
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
					<select class="form-control" id="hs_degree" name="hs_degree" required="required">
						<option value="">Select Degree</option>
						<option value="Achiever" >Achiever</option>
						<option value="With Honors" >With Honors</option>
						<option value="With High Honors" >With High Honors</option>
						<option value="With Highest Honors" >With Highest Honors</option>
						<option value="None" >None</option>
					</select>
				</div>

				<div class="col-md-2 col-sm-2 col-xs-12">
				  <select class="form-control" name="hs_year_level" id="hs_year_level" required="required">
				  	<option value="">Select Year</option>
					<option value="Grade 7/First Year" <?php echo (isset($_SESSION['user_data']['hs_year_level']) && $_SESSION['user_data']['hs_year_level'] === 'Grade 7/First Year') ? 'selected' : ''; ?>>
					Grade 7/First Year</option>
					<option value="Grade 8/Second Year" <?php echo (isset($_SESSION['user_data']['hs_year_level']) && $_SESSION['user_data']['hs_year_level'] === 'Grade 8/Second Year') ? 'selected' : ''; ?>>
					Grade 8/Second Year</option>
					<option value="Grade 9/Third Year"<?php echo (isset($_SESSION['user_data']['hs_year_level']) && $_SESSION['user_data']['hs_year_level'] === 'Grade 9/Third Year') ? 'selected' : ''; ?>>
					Grade 9/Third Year</option>
					<option value="Grade 10/Fourth Year" <?php echo (isset($_SESSION['user_data']['hs_year_level']) && $_SESSION['user_data']['hs_year_level'] === 'Grade 10/Fourth Year') ? 'selected' : ''; ?>>
					Grade 10/Fourth Year</option>
					<option value="Grade 12/Senior High/Graduating"<?php echo (isset($_SESSION['user_data']['hs_year_level']) && $_SESSION['user_data']['hs_year_level'] === 'Grade 12/Senior High/Graduating') ? 'selected' : ''; ?>>
					Grade 11/Senior High 1</option>
					<option value="Grade 12/Senior High/Graduating"<?php echo (isset($_SESSION['user_data']['hs_year_level']) && $_SESSION['user_data']['hs_year_level'] === 'Grade 12/Senior High/Graduating') ? 'selected' : ''; ?>>
					Grade 12/Senior High 2/Graduating</option>
					<option value="Graduate"<?php echo (isset($_SESSION['user_data']['hs_year_level']) && $_SESSION['user_data']['hs_year_level'] === 'Graduate') ? 'selected' : ''; ?>>
					Graduate</option>					
				  </select>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="form-control col-md-7 col-xs-12" required="required" type="text" id="hs_date_attendance" name="hs_date_attendance" placeholder="Year Ended" 
				  data-toggle="tooltip" data-placement="left" value="<?php echo isset($_SESSION['user_data']['hs_date_attendance']) ? $_SESSION['user_data']['hs_date_attendance'] : ''; ?>" />
				</div>
			  </div>
			  	<script>
					$('#hs_year_level').val();
				</script>
					
			  <div class="form-group">
				<label class="control-label col-md-2 col-sm-2 col-xs-6">College: </label>
				<div class="col-md-4 col-sm-2 col-xs-12">
				  <input class="date-picker form-control col-md-7 col-xs-12" type="text" id="suc_name" name="suc_name" placeholder="College Name (Leave as Blank if None)" 
				  value="<?php echo isset($_SESSION['user_data']['suc_name']) ? $_SESSION['user_data']['suc_name'] : ''; ?>"  />
				</div>

				<div class="col-md-2 col-sm-2 col-xs-12">
					<select class="form-control" name="suc_course" id="suc_course">
						<option value="">Select Degree</option>
						<option value="Dean's Lister">Dean's Lister</option>
						<option value="None">None</option>
					</select>
				</div>

				<div class="col-md-2 col-sm-2 col-xs-12">
				  <select class="form-control" name="suc_year_level" id="suc_year_level">
					<option value="">Select Year</option>
					<option value="First Year" <?php echo (isset($_SESSION['user_data']['suc_year_level']) && $_SESSION['user_data']['suc_year_level'] === 'First Year') ? 'selected' : ''; ?>>
					First Year</option>
					<option value="Second Year"<?php echo (isset($_SESSION['user_data']['suc_year_level']) && $_SESSION['user_data']['suc_year_level'] === 'Second Year') ? 'selected' : ''; ?>>
					Second Year</option>
					<option value="Third Year"<?php echo (isset($_SESSION['user_data']['suc_year_level']) && $_SESSION['user_data']['suc_year_level'] === 'Third Year') ? 'selected' : ''; ?>>
					Third Year</option>
					<option value="Fourth Year"<?php echo (isset($_SESSION['user_data']['suc_year_level']) && $_SESSION['user_data']['suc_year_level'] === 'Fourth Year') ? 'selected' : ''; ?>>
					Fourth Year</option>					
					<option value="Fourth Year/Graduating" <?php echo (isset($_SESSION['user_data']['suc_year_level']) && $_SESSION['user_data']['suc_year_level'] === 'Fourth Year/Graduating') ? 'selected' : ''; ?>>
					Fourth Year/Graduating</option>
					<option value="Fifth Year/Graduating"<?php echo (isset($_SESSION['user_data']['suc_year_level']) && $_SESSION['user_data']['suc_year_level'] === 'Fifth Year/Graduating') ? 'selected' : ''; ?>>
					Fifth Year/Graduating</option>
					<option value="Graduate"<?php echo (isset($_SESSION['user_data']['suc_year_level']) && $_SESSION['user_data']['suc_year_level'] === 'Graduate') ? 'selected' : ''; ?>>
					Graduate</option>					
				  </select>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
				  <input class="form-control col-md-7 col-xs-12" type="text" id="suc_date_attendance" name="suc_date_attendance" placeholder="Year Ended" 
				  data-toggle="tooltip" data-placement="left" value="<?php echo isset($_SESSION['user_data']['suc_date_attendance']) ? $_SESSION['user_data']['suc_date_attendance'] : ''; ?>" />
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
							<option name="spes_times" value="0" <?php echo isset($_SESSION['user_data']['spes_times']) && $_SESSION['user_data']['spes_times'] === '0' ? 'selected' : ''; ?>>0 (First Time)</option>
							<option name="spes_times" value="1" <?php echo isset($_SESSION['user_data']['spes_times']) && $_SESSION['user_data']['spes_times'] === '1' ? 'selected' : ''; ?>>1</option>
							<option name="spes_times" value="2" <?php echo isset($_SESSION['user_data']['spes_times']) && $_SESSION['user_data']['spes_times'] === '2' ? 'selected' : ''; ?>>2</option>
							<option name="spes_times" value="3" <?php echo isset($_SESSION['user_data']['spes_times']) && $_SESSION['user_data']['spes_times'] === '3' ? 'selected' : ''; ?>>3</option>
							<option name="spes_times" value="4" <?php echo isset($_SESSION['user_data']['spes_times']) && $_SESSION['user_data']['spes_times'] === '4' ? 'selected' : ''; ?>>4</option>
						</select>
						<br><br>
					</div>
				</div>
				</select>
					<br><br>
				</div>
					
			  </div>				
			  <div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
					<button class="btn btn-primary" type="button" onclick="cancelEditProfile()">Cancel</button>
					<button class="btn btn-warning" type="reset">Reset</button>
					<button class="btn btn-success" type="submit" onclick="saveFormData()" name="next" id="nextButton" style="display: none;">Next</button>
					<button class="btn btn-success" type="submit" name="update" id="updateButton" style="display: none;">Update</button>
				</div>
        </div>
    	</form>
		  </div>
		</div>
	  </div>
	</div>
	
<script>
	const applicationTypeDropdown = document.getElementById('type_Application');
	const nextButton = document.getElementById('nextButton');
	const updateButton = document.getElementById('updateButton');

	applicationTypeDropdown.addEventListener('change', function() {
	const selectedValue = applicationTypeDropdown.value;
	
	if (selectedValue === 'New Applicants') {
		nextButton.style.display = 'inline-block';
		updateButton.style.display = 'none';
	} else if (selectedValue === 'Renewal') {
		updateButton.style.display = 'inline-block';
		nextButton.style.display = 'none';
	}
	});


</script>
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
	<!-- Include jQuery -->
	<script>

		$(document).ready(function() {
		$('#type_Application').change(function() {
		console.log("Type changed"); // Log to check if the type change event is triggered

		var selectedType = $(this).val();

		if (selectedType === 'New Applicants') {
			console.log("Fetching user data"); // Log to check if it enters this condition

			$.ajax({
				type: 'POST',
				url: 'get_user_data2.php',
				dataType: 'json',
				success: function(data) {
					console.log("Received user data:", data); // Log the received user data
						$('#first_Name').val(data.gname);
						$('#middle_Name').val(data.mname);
						$('#last_Name').val(data.lname);
						$('#email').val(data.email);  
						$('#suffix').val(data.suffix); 
						$('input[name="sex"]').each(function() {
							if ($(this).val() === data.gender) {
								$(this).prop('checked', true); // Set the checked attribute
							}
						});
				},
				error: function(xhr, status, error) {
					console.error("Error fetching user data:", error); // Log if an error occurs
					// Handle error if data retrieval fails
				}
			});
		}
		});
		});

		</script>
	<script>

		$(document).ready(function() {
    $('#type_Application').change(function() {
        console.log("Type changed"); // Log to check if the type change event is triggered

        var selectedType = $(this).val();

        if (selectedType === 'Renewal') {
            console.log("Fetching user data"); // Log to check if it enters this condition

            $.ajax({
                type: 'POST',
                url: 'get_user_data.php',
                dataType: 'json',
                success: function(data) {
                    console.log("Received user data:", data); // Log the received user data
						$('#first_Name').val(data.first_Name);
						$('#middle_Name').val(data.middle_Name);
						$('#last_Name').val(data.last_Name);
						$('#suffix').val(data.suffix); 
						$('#birthday').val(data.birthday);
						$('#place_of_birth').val(data.place_of_birth);
						$('#citizenship').val(data.citizenship);
						$('#mobile_no').val(data.mobile_no);
						$('#email').val(data.email);    
						$('input[name="civil_status"]').each(function() {
							if ($(this).val() === data.civil_status) {
								$(this).prop('checked', true); 
							}
						});
						$('input[name="sex"]').each(function() {
							if ($(this).val() === data.sex) {
								$(this).prop('checked', true); // Set the checked attribute
							}
						});
						$('input[name="spes_type"]').each(function() {
							if ($(this).val() === data.spes_type) {
								$(this).prop('checked', true); // Set the checked attribute
							}
    					});
						$('input[name="parent_status"]').each(function() {
							if ($(this).val() === data.parent_status) {
								$(this).prop('checked', true); // Set the checked attribute
							}
						});
						$('input[name="parents_displaced"]').each(function() {
							if ($(this).val() === data.parents_displaced) {
								$(this).prop('checked', true); // Set the checked attribute
							}
    					});
						var citizenshipValue = data.citizenship;

						// Set the value of the dropdown based on data.citizenship
						$('#citizenship').val(citizenshipValue);
						$('#no_street').val(data.no_street);
						$('#province_id').val(data.province_id);
						$('#city_municipality_id').val(data.city_municipality_id);
						$('#barangay_id').val(data.barangay_id);
						$('#no_street2').val(data.no_street2);
						$('#province_id2').val(data.province_id2);
						$('#city_municipality_id2').val(data.city_municipality_id2);
						$('#barangay_id2').val(data.barangay_id2);
						$('#father_first_name').val(data.father_first_name);
						$('#father_middle_name').val(data.father_middle_name);
						$('#father_last_name').val(data.father_last_name);
						$('#father_contact_no').val(data.father_contact_no);
						$('#mother_first_name').val(data.mother_first_name);
						$('#mother_middle_name').val(data.mother_middle_name);
						$('#mother_last_name').val(data.mother_last_name);
						$('#mother_contact_no').val(data.mother_contact_no);
						$('#elem_name').val(data.elem_name);
						$('#year_grade_level').val(data.year_grade_level);
						$('#elem_date_attendance').val(data.elem_date_attendance);
						$('#hs_name').val(data.hs_name);
						$('#hs_degree').val(data.hs_degree);
						$('#hs_year_level').val(data.hs_year_level);
						$('#hs_date_attendance').val(data.hs_date_attendance);
						$('#suc_name').val(data.suc_name);
						$('#uc_course').val(data.suc_course);
						$('#suc_year_level').val(data.suc_year_level);
						$('#suc_date_attendance').val(data.suc_date_attendance);
						$('#status').val(data.status);
						$('#spes_times').val(data.spes_times);

                },
                error: function(xhr, status, error) {
                    console.error("Error fetching user data:", error); // Log if an error occurs
                    // Handle error if data retrieval fails
                }
            });
        }
    });
});

	</script>
	
        </div>


        <!-- footer content -->
        <footer id="mainFooter">
            &copy; Copyright 2023 | Online Special Program for Employment of Student (SPES)
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