<!--
	Author: W3layouts
	Author URL: http://w3layouts.com
	License: Creative Commons Attribution 3.0 Unported
	License URL: http://creativecommons.org/licenses/by/3.0/
-->


<!DOCTYPE html>
 	<?php	
		include_once'../includes/dBconnect.php';
		include_once '../includes/session.php';
		include_once '../includes/function.php';
		if (session_status() !== PHP_SESSION_ACTIVE) {sec_session_start();}//start the session
		// check if user has logged in.  If not redirect to index page
		$userID = $_SESSION['user']['id'];
    $token =  $_SESSION['token'];

    pageAccess($token, $userID, $db);
    $username = $_SESSION['user']['username'];
if($_SESSION['permissions']['attendance'] =='none'){
	header('location:dashboard.php');
}
	?>

<html lang="en">
<!-- Head -->
<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-118006201-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-118006201-1');
</script>

	<title>Attendance Page</title>
	<!-- Meta-Tawgs -->
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="keywords" content="Associate a Responsive Web Template, Bootstrap Web Templates, Flat Web Templates, Android Compatible Web Template, Smartphone Compatible Web Template, Free Webdesigns for Nokia, Samsung, LG, Sony Ericsson, Motorola Web Design">
		<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
	<link rel="stylesheet" type="text/css" href="../css/InisopeDirectory.css" />
	<!-- //Meta-Tags -->
	<!-- Custom-Theme-Files -->
	<link rel="stylesheet" href="../css/bootstrap.min.css" type="text/css" media="all">
	<link rel="stylesheet" href="../css/style.css" type="text/css" media="all">
	<link rel="stylesheet" href="../css/SunnyViewTheme.css" type="text/css" media="all">
	<link rel="stylesheet" href="../css/font-awesome.min.css" />

	<!-- //Custom-Theme-Files -->
	<!-- Web-Fonts -->
	<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800" 	type="text/css">
	<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Montserrat:400,700" 	type="text/css">
	<!-- //Web-Fonts -->
	<!-- Default-JavaScript-File -->
	<script type="text/javascript" src="../js/jquery-2.1.4.min.js"></script>
	<script type="text/javascript" src="../js/bootstrap.min.js"></script>
	
	<script src="../js/main.js"></script>
	<script src="../js/permission.js"></script>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	

</head>
<!-- //Head -->
<!-- Body -->
<body>
	
	<!-- Header -->
	<Header class="font">
			<div class="container">
				<nav class="nav-Header navList-Header" aria-label="Site Navigation">
					<!-- Brand and toggle get grouped for better mobile display -->
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<div class="collapse navbar-collapse nav-wil" id="bs-example-navbar-collapse-1">
						<ul class="navList">
							<li class="dropdown" style="padding: 0;">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Account <b class="caret"></b></a>
									<ul class="dropdown-menu agile_short_dropdown">
										<li style="padding: 0;"><a href="dashboard">Dashboard</a></li>
										<li style="padding: 0;"><a href="logout">Logout</a></li>
									</ul>
							</li>
							<li class="dropdown" style="padding: 0;">
								<ul class ="dropdown-menu-button">
									<li><a class="navHidden" href="dashboard">Dashboard</a></li>
									<li><a class="navHidden" href="logout">Logout</a></li>
								</ul>
							</li>							
						</ul>
					</div>
				<!-- /navbar-collapse -->
				</nav>
					<div class = "TitlePosition TitleLeft">
						<h1 class="TitleSm-view"><?php echo"$username"; ?></h1>
						<h1 class="TitleLg-view"><?php echo"$username"; ?></h1>
					</div>
			</div>
	</header>
	<!-- //Header -->
	

	<!-- Section -->
	<section class="sectionContent">
		<div class="container">
			<h1 class="section_head">Attendance Page</h1>
			<div class="row">
				<div class="col-sm-4 dash-grids">
					<div class="dash-grid-img"> 
						<a href="#" id="btn_attendance" class="icons" onClick="permission_click(this,'<?php echo $_SESSION['permissions']['attendance'];?>')">
							<img src="../images/calendarCheckmark.png" alt="Take Attendance">
            </a>
					</div>
					<h4>Take Attendance</h4> 
					<p>Record resident daily attendanc.</p>
				</div>			
				<div class="col-sm-4 dash-grids">
					<div class="dash-grid-img"> 
						<a href="#" id="btn_absences" class="icons" onClick="permission_click(this,'<?php echo $_SESSION['permissions']['attendance'];?>')">
              <img src="../images/if_personal_1447.png" alt="User Access">
            </a>
					</div>
					<h4>Absence</h4> 
					<p>Track known current and future absences due to reasons such as doctor's appointments or vacations.</p>
				</div>				
				<div class="clearfix"> 
				</div>
			</div>
		</div>
	</section>
	<!-- //about -->
	<!-- footer -->
		<footer>
			<div class="container">
				<div class="background">
					<div class="copywrite">
						<p>© 2017 All rights reserved.
					</div>
				</div>
			</div>
		</footer>
				<!--Modal HR Change Request Success message-->
				<div class="modal fade" id="myModal" role="dialog">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title"></h4>
							</div>
							<div class="modal-body">
              </div>
							<div class="modal-footer">
							</div>
						</div>
					</div>
        </div>

	        <!-- modal HR Change Request form -->
        <div class="modal fade" id="contact_dialog" role="dialog">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title">HR Change Request</h4>
							</div>
							<div class="modal-body">
								<div class="sectionSearch">
									<form id="contact_form" action="hrChangeRequest.php" method="POST">
<!--row-->												
										<div class="row rowHRChange">
											<div class="col-sm-6">
												<h4>Employee</h4>
												<select name="formEmployee" id="formEmployee" class="employee">
													<option value =""> - select - </option>
													<?php 
													if ($resNSearch->num_rows > 0) 
													{
														while ($row = $resNSearch->fetch_assoc())
														{
														/*sql query option for dropdown*/
															echo "<option value = '{$row['EMPLOYEE_ID']} {$row['LAST_NAME']}, {$row['FIRST_NAME']} {$row['PHONE_NUMBER']} {$row['DEPARTMENT_ID']}'>";
															echo"<div class='col-sm-2'>" . $row["LAST_NAME"] . " " . "</div>";	
															echo"<div class='col-sm-2'>" . $row["FIRST_NAME"] . " " . "</div>";																		
															echo "</option>";
														/*end of sql query option*/			
														}
													}												
													?>
												</select>
											</div>	
										</div>
<!--row-->
										<div class="row rowHRChange underline">
											<div class="col-sm-12">
											<!--heading and paragraph are populated when an option is selected in the dropdown box.-->
												<h4 id="currentheading"></h4>
												<div class = "col-sm-1">
													<h5 id="hID"></h5>
													<p id="ResEmployeeID" ></p>	
												</div>
												<div class = "col-sm-2">
													<h5 id="hLast"></h5>
													<p id="ResEmployeeLast" ></p>															
												</div>
												<div class = "col-sm-2">
													<h5 id="hFirst"></h5>
													<p id="ResEmployeeFirst" ></p>															
												</div>
												<div class = "col-sm-4">
													<h5 id="hPhone"></h5>
													<p id="ResEmployeePhone" ></p>															
												</div>
												<div class = "col-sm-1">
													<h5 id="hDept"></h5>
													<p id="ResEmployeeDept" ></p>		
												</div>
											</div>
										</div>
<!--row-->
										<div class="row rowHRChange">
											<h2>New Employee Information</h2>
										</div>	
<!--row-->						<!--New employee information-->
										<div class="row rowHRChange">
											<div class="col-sm-6">
												<label for="formEmployeeID" >Employee ID:</label>
												<input type="text" name="formEmployeeID" id="formEmployeeID" readonly>
											</div>
										</div>												
										<div class="row rowHRChange">												
											<div class="col-sm-6">
												<label for="newFirst">First Name:</label>
												<input type="text" name = "newFirst" id="newFirst" >													
											</div>
											<div class="col-sm-6">
												<label for="newLast">Last Name:</label>
												<input type="text" name="newLast" id="newLast" >													
											</div>												
										</div>	
										<div class="row rowHRChange">
									  	<div class="col-sm-6">											
												<label for="newPhone">Phone Number:</label>
												<input type="text" name="newPhone" id="newPhone" >													
											</div>
									  	<div class="col-sm-6">
												<label for="newDept">Department:</label>
												<select name="FormDepartment" id="newDept" >
													<option value =""> - select - </option>
													<?php 
														if ($resDep->num_rows > 0) 
														{
															while ($row = $resDep->fetch_assoc())
															{
    														echo "<option value = '{$row['DEPARTMENT_ID']}'>{$row['DEPARTMENT_ID']} - {$row['DEPARTMENT_NAME']}</option>";
															}
															echo "</select>";
														}			
													?>
												</select>													
											</div>
										</div>
									</form>
								</div>	
              </div>
							<div class="modal-footer">
								<button type="button" data-dismiss="modal">Close</button>
								<button type="button" id="submitForm"  data-toggle="modal" data-target="#myModal" data-dismiss="modal">Submit</button>					
							</div>
						</div>
					</div>
        </div>


				<!-- //modal -->  

</body>
<!-- //Body -->
</html>