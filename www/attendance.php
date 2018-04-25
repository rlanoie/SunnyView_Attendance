<!--
	Author: W3layouts
	Author URL: http://w3layouts.com
	License: Creative Commons Attribution 3.0 Unported
	License URL: http://creativecommons.org/licenses/by/3.0/
-->


<!DOCTYPE html>
<html lang="en">
 	<?php	
		include_once'../includes/dBconnect.php';
		include_once '../includes/session.php';
		include_once '../includes/function.php';
		//include_once '../includes/residentFunctions.php';
		include_once '../includes/attendanceFunctions.php';
    if (session_status() !== PHP_SESSION_ACTIVE) {sec_session_start();} //start the session

// check if user has logged in.  If not redirect to index page
    $userID = $_SESSION['user']['id'];
    $token =  $_SESSION['token'];
    pageAccess($token, $userID, $db);
  
    $username = $_SESSION['user']['username'];
	 
	
	date_default_timezone_set('America/Los_Angeles');
	$today = date("Y-m-d");

	$attendanceProgress = attendanceStatus($today, $db);
	$queryResults = attendanceList($today, $db);

	$queryAbsences = absenceByDate($db);
	$resDropdown = getDropdownResidents($today, $db);
	//$resDropdown = residentListALL($db);
	$dropDown_Count = $resDropdown['count'];
	?>
<!-- Head -->
<head>
	<title>Attendance</title>
	<!-- Meta-Tawgs -->
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="keywords" content="Associate a Responsive Web Template, Bootstrap Web Templates, Flat Web Templates, Android Compatible Web Template, Smartphone Compatible Web Template, Free Webdesigns for Nokia, Samsung, LG, Sony Ericsson, Motorola Web Design">
		<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, {passive: true}); function hideURLbar(){ window.scrollTo(0,1); } </script>
	
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
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	
	<!-- Default-JavaScript-File -->
	<script type="text/javascript" src="../js/jquery-2.1.4.min.js"></script>
	<!--<script type="text/javascript" src="../js/bootstrap.min.js"></script>-->
	
	<script src="../js/main.js"></script>
	<script src="../js/ajax.js"></script>
	<script src="../js/attendance.js"></script>
	
	
	
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	
	<!--<script src="https://code.jquery.com/jquery-1.12.3.min.js"></script>-->
	
  
	<script>
		$(document).ready(function(){
			getUserPermission('attendance', permission()); //check what permissions user has for this page
			formatChanges("<?php echo $attendanceProgress ?>");
			$("#formResHID").focus();
		});
		//Returns the current permission of the current user.
		function permission(){
			return("<?php echo $_SESSION['permissions']['attendance'] ?>");
		}
		//Returns the current status of the attendance
		function attendanceProgress(){
			return "<?php echo $attendanceProgress ?>";
		}
		
		function returnCurrentDate(){
			return "<?php echo $today ?>";
		}
		

	</script>

</head>
<!-- //Head -->
<!-- Body -->
<body>
	<!-- Header -->
		<Header class="font">
			<div class="container">
				<nav class="nav-Header navList-Header" aria-label="Site Navigation">
					<!-- Brand and toggle get grouped for better mobile display -->
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					
					<div class="collapse navbar-collapse nav-wil" id="bs-example-navbar-collapse-1">
						<ul class="navList">
							<li id="navigationDropDown" class="dropdown" style="padding: 0;">
								<a href="#"  class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Account <b class="caret"></b></a>
									<ul class="dropdown-menu agile_short_dropdown">
										<li style="padding: 0;"><a href="attendance-page">Attendance</a></li>                    
										<li style="padding: 0;"><a href="dashboard">Dashboard</a></li>                    
										<li style="padding: 0;"><a href="logout">Logout</a></li>
									</ul>
							</li>
						<li class="dropdown" style="padding: 0;">
								<ul class ="dropdown-menu-button">
									<li><a class="navHidden" href="attendance-page">Attendance</a></li>
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
  </Header>
	<!-- Section -->
	 <section id = "sectionBody" class="theme sectionAttendance sectionContent">
			<div class="container">
				<Header>
					<h1 class="header">Attendance</h1>
				</Header>
				<section id = "attendanceMeta">
					
					<h4 id="headerDate" class = "pdf-print-only">Attendance Date: <?php echo date("m-d-Y", strtotime($frmDate));?></h4>
					<p id="headerStatus"></p>
					
				</section>
				<section id="AttendanceBody" class="bodyContent">
<!--ASIDE-->
					<aside>
						<p id = "displayStatus"></p>
						<header>
							<ul style="list-style-type:none">
								<li><a href="#" id="startClick" onclick="startAttendance()" aria-label="Start Attendance">Start</a></li>
								<li><a href=# id="closeClick" onclick="closeAttendance()">Close Day</a></li>
							</ul>
						</header>
						<section>
							<h4 class="bold">
								Add Resident
							</h4>
								<form method="POST" name="takeAttendance" id="takeAttendance" >
									<fieldset>
										<label for="frmDate" class="formlabel">Date:</label> 
										<input type="date" name="frmDate" id="frmDate" onchange="dateHandler(event)" value="<?php echo $frmDate;?>" /> 
									</fieldset>
									<fieldset id="disableFilter" class="disableform" disabled>
										<label for="formFilterHID" class="formlabel">By Key Fob #:</label> 
										<input type="password" name="formResHID" id="formResHID"/> 
										<h4>- OR -</h4> 
										<label for="formFilterHID" class="formlabel">By Name:</label> 
										<select name="residentID" id="residentID" size="4" onchange="clearHID()">
											<option value =""> - select - </option>
												<?php
													for ($i = 0; $i < $dropDown_Count; $i++) {  
													echo "<option value='{$resDropdown['row'][$i]['id']}'> {$resDropdown['row'][$i]['resident']}</option>";}
												?>
										</select>
										<h4 hidden>UserID:</h4>
										<input type="hidden" name="formUserID" id="formUserID" value="<?php echo $userID;?>" hidden/>
                    <input type="hidden" id="token" name="token" value="<?php echo $token; ?>">
										<input type="hidden" name="formCode" id="formCode" value="2" hidden/> 
										<div class="clearfix"> </div>
										<button type="submit" name="btn_AddAttendee" class="button" id="btn_AddAttendee" >Add</button>										
										<div class="clearfix"> </div>
									</fieldset>
								</form>
							</section>
						<section>
							<ul style="list-style-type:none">
								<a href="" data-toggle="modal" id="modalOpen_VerifyAbsence" >Verify Absences</a>
							</ul>
						</section>
						<section>
							<ul style="list-style-type:none">
								<a href=# onclick="generateReport_absence('print');">Print</a>
							</ul>
						</section>
					</aside>  
<!--/ASIDE-->					
				<div id="grid" class="body-container">
						<header>
							<h2> Attendance Roster </h2>
							<p id="attendanceCount"></p>
							<div id="gridlist-header" class='team-row sectionResults'>
								<div class = "row text"> 
									<div class = "col-sm-1">No.</div>
									<div class = "col-sm-4 pdf-print-only">Recorded</div>                  
									<div class = "col-sm-2 pdf-noPrint">Time</div>                  
									<div class = "col-sm-3">Resident</div>
									<div class = "col-sm-2">Status</div>
									<div class = "col-sm-3">Employee</div>
								</div>
							</div>
						</header> 		
						<section class = "gridcontainer">
							<div id="gridlist" class='team-row sectionResults'>
								<?php
									if ($queryResults ['count'] > 0) {
										$count = 0;
  	                foreach($queryResults ['row'] as $column) {	
											$count++;
											$recordedDate = date("m-d-Y", strtotime($column['date']));
											echo "<a href='#' class='modalOpen_Attendee' data-toggle='modal'>";
												echo"<div class='row rowResults'>"; 
													echo"<div class='col-sm-1 rowCount'>". $count . "</div>";
													echo"<div class='col-sm-3 rowId' hidden>". $column['ResID'] . "</div>";
													echo"<div class='col-sm-4 row_recorded pdf-print-only'>". $recordedDate . "&emsp;  " . substr($column['timestamp'], 11) . "</div>";
													echo"<div class='col-sm-2 timestamp pdf-noPrint'>". substr($column['timestamp'], 11) . "</div>";
													echo"<div class='col-sm-3 row_resident'>" . $column['resident'] . "</div>";
													echo"<div class='col-sm-2 row_status'>" . $column['code'] . "</div>";
													echo"<div class='col-sm-3 row_employee'>" . $column['employee'] . "</div>";
												echo"</div>";
											echo"</a>";
    								}
									}
								?>
								</div>
						</section>
						<footer></footer>
					</div>
				</section>
				<section id="attendanceErrorLog"></section>
				<section id="attendanceMissing">
					<div id="gridMissing" class="body-container">
						<header>
							<h2> Missing Residents</h2>
							<p id="missingDetails"></p>
							<div id="gridlistMissing-header" class='team-row sectionResults'>
								<div class = "row text"> 
									<div class = "col-sm-2">ID</div>                  
									<div class = "col-sm-4">Resident</div>
								</div>
							</div>
						</header>
						<section class = "gridcontainer">
							<div id="gridlist_Missing" class='team-row sectionResults'>
								
							</div>
						</section>
					</div>
				</section>
			</div>
		</section>
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
	
<!-- MODAL Absence Recording -->
  <div class="modal theme fade" id="modal_AbsenceVerification" role="dialog" >
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button id="modalClose" type="button" class="close" data-dismiss="modal">&times;</button>
						<h1 class="modal-title">Reported Absences</h1>
					</div>
					<div class="modal-body">
						<div class="modal-container">
							<div class = "body">
						<!--Displays the user's current information-->	
								<div id="modal-bodyHeader" class="row underline">
									<h2 id="modal-AddTitle">Verify Absences</h2>
								<p>Place a checkmark next to the residents who are confirmed absent for today.</p>
								<p id="modalAbsenceAlert" class="errorMsg"></p>
								</div>
								<div class="formContent">
									<div id="gridlist_Modal" class='team-row sectionResults'>
										
										<?php
											if ($queryAbsences ['count'] > 0) {
  	      		          foreach($queryAbsences ['row'] as $column) {	
													
													echo "<div data-toggle='modal'>";
														$leaving = date("m-d-Y", strtotime($column['Leaving']));
														$returning = date("m-d-Y", strtotime($column['Returning']));
														echo"<div class='row rowResults rowResID_".$column['resID']."'>"; 
															echo"<div class='col-sm-1 selected'>";
																echo "<div class='square'>";
																	echo"<input type='checkbox' class = 'absenceConfirmation' name='absenceConfirm' id='resCheckbox_".$column['resID']."'  value='".$column['resID']."' '>";
																	echo "<label for='resCheckbox_".$column['resID']."' id='labelRResident' class='checkable' ></label>";
																echo"</div>";
															echo"</div>";
															echo"<div class='col-sm-1 rowId' hidden>". $column['resID'] . "</div>";
															echo"<div class='col-sm-9 row_resident bold'>" . $column['resident'] . "</div>";
															echo "<div id='ignoreAbsence' class='col-sm-2 row_Ignore' hidden>" ."Ignore".  "</div>";
															echo"<div class='col-sm-3 row_reasonID' hidden>" . $column['reasonID'] . "</div>";
															echo"<div class='col-sm-3 italic row_reason'>" . $column['Code'] . "</div>";
															echo "<div class='col-sm-7 row_leaving' hidden>" . $leaving .  "</div>";
															echo "<div class='col-sm-7 row_returning' hidden >" . $returning .  "</div>";
															if (is_null($column['Returning'])){
																echo "<div class='col-sm-7 row_dates italic'>" . $leaving .  "</div>";
															}else{
																echo"<div class='col-sm-7 row_dates italic'> " . $leaving . " - " . $returning . "</div>";	
															}
														echo"</div>";
													echo"</div>";
    										}
											}
										?>
									</div>
								</div>
							</div>	
						</div>
					</div>
					<div class="modal-footer">
						<div class="row">
							
							<button type="submit" id="submitModal_VerifyAbsence" name="submitModal_VerifyAbsence" onclick='absenceVerificationProcess();' />Submit</button>
							
						</div>
					</div>
				</div>
			</div>
		</div>

</body>
<!-- //Body -->
</html>