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
		include_once '../includes/residentFunctions.php';
		include_once '../includes/attendanceFunctions.php';
    if (session_status() !== PHP_SESSION_ACTIVE) {sec_session_start();}//start the session
    
    // check if user has logged in.  If not redirect to index page
    $userID = $_SESSION['user']['id'];
    $token =  $_SESSION['token'];
	  pageAccess($token, $userID, $db);

    $username = $_SESSION['user']['username'];
	

if($_SESSION['permissions']['attendance'] =='none'){
	header('location:dashboard.php');
}
	date_default_timezone_set('America/Los_Angeles');
	$today = date("Y-m-d");


		$queryResults = defaultAbsenceQuery($db);
		$count = $queryResults['count'];

		$resDropdown = residentListALL($db);
		$dropDown_Count = $resDropdown['count'];
		$attendCodeDropdown = attendanceCode_Dropdown($db);
		$attendDropdown_Count = $attendCodeDropdown['stmt']->rowCount();
	
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

	<title>Absence</title>
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
		<!--<script type="text/javascript" src="../js/bootstrap.min.js"></script>-->
	
		<script src="../js/main.js"></script>
		<script src="../js/ajax.js"></script>
		<script src="../js/absence.js"></script>
	
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<!-- 	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>-->
	
	
	<script>
		$(document).ready(function(){
			getUserPermission('absence', permission()); //check what permissions user has for this page
		});
		
		function permission(){
			return("<?php echo $_SESSION['permissions']['attendance'] ?>");
		}
	
	</script>

</head>
<!-- //Head -->
<!-- Body -->
<body>
	<!-- Header -->
		<Header #id = "pageHeader" class="font">
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
					<h1 class="header">Absence</h1>
				</Header>
				<section id = attendanceMeta>
					<P>
						A list of all reported upcoming resident absemces.
						If a returning date is not indicated the resident has only reported a single day of absence.
						<br><br>
						Assorted by date leaving:
					</P>
				</section>
				<section id="absenceBody" class="bodyContent">
<!--ASIDE-->
					<aside>
						<header>
							<ul style="list-style-type:none">
								<li><a href="#" class="addAbsence" onclick="openModal_AddAbsence() ">Record Absence</a></li>
							</ul>
						</header>
						<section>
							<ul style="list-style-type:none">
								<li><a href="#" onclick="printByTag('sectionBody');">Print</a></li>
							</ul>
						</section>
					</aside>  
<!--/ASIDE-->					
						<div id="grid" class="body-container">
							<header>
								<div id="gridlist-header" class='team-row sectionResults'>
									<div class = "row text">                  
										<div class = "col-sm-1">ID</div>
										<div class = "col-sm-3">Resident</div>
										<div class = "col-sm-2">Absence</div>
										<div class = "col-sm-2">Leaving</div>
										<div class = "col-sm-2">Returning</div>
										<div class = "col-sm-2">Employee</div>
									</div>
								</div>
							</header> 				
							<section class = "gridcontainer">
								<div id="gridlist" class='team-row sectionResults'>
									
								<?php
									if ($count > 0) {
  	                foreach($queryResults ['row'] as $column) {			
											echo "<a href='#' class='changeAbsence'>";
												$leaving = date("m-d-Y", strtotime($column['Leaving']));
												$returning = date("m-d-Y", strtotime($column['Returning']));
												$recordedDate = date("m-d-Y H:i:s", strtotime($column['createdOn']));
												$today = date("m-d-Y");
											
												if(($leaving==$today) OR ($returning==$today)){
													echo"<div class='row rowResults highlight rowResID_".$column['resID']." rowLeaving_".$leaving."'>"; 
												}else{
													echo"<div class='row rowResults rowResID_".$column['resID']." rowLeaving_".$leaving."'>"; 	
												}
												
													echo"<div class='col-sm-1 rowId'>". $column['resID'] . "</div>";
													echo"<div class='col-sm-3 rowResident'>". $column['resident'] . "</div>";
													echo"<div class='col-sm-2 row_code'>" . $column['Code'] . "</div>";
														
													echo"<div class='col-sm-2 row_leaving'>" . $leaving . "</div>";
													
													if (is_null($column['Returning'])){
														echo"<div class='col-sm-2 row_Returning'>" . $column['Returning'] . "</div>";
													}else{
														echo"<div class='col-sm-2 row_Returning'>" . $returning . "</div>";	
													}
													echo"<div class='col-sm-2 row_Employee'>" . $column['employee'] . "</div>";
													echo"<div class='col-sm-2 row_localTimeStamp' hidden>" . $recordedDate . "</div>";
													echo"<div class='col-sm-2 row_lastUpdate' hidden>" . $column['lastUpdate'] . "</div>";
													echo"<div class='col-sm-2 row_updatedBy' hidden>" . $column['updator'] . "</div>";
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
	
<!-- Absence Recording -->
	  <div class="modal theme fade" id="absences" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h1 id = "modal-AddTitle" class="modal-title">Record Absence</h1>
						<h1 id = "modal-ChangesTitle" class="modal-title">Absence Record</h1>
					</div>
					<div class="modal-body">
						<div class="modal-container">
							<div id="modal-bodyContent" class = "body">
							<!--Displays the user's current information-->	
								<div id="modal_Row_headerDetails" class = "row">
									<div class="col-sm-8">
										<h4>Current Details</h4>
										<p id="Modal_CurrResult"></p>
									</div>
									<div class="col-sm-3" id = "colDeleteAbsence">
    								<button type="submit" id="modalbtn_deactivateAbsence" name="modalbtn_deactivateAbsence" onclick="btnDeleteRecord()" aria-label="Delete">Delete</button>
									</div>
								</div>
								<div id="recordHistory" class="row underline">
									<div class="col-sm-6">
										<p id="row_CreatedDetails"></p>
									</div>
									<div class="col-sm-6">
										<p id="row_UpdatedDetails"></p>
									</div>
									<div class="col-sm-6">
										<p id="row_DeletedDetails"></p>
									</div>
								</div>
								<div id="modalAbsenceForm" class="formContent">										
									<form id="frmAbsence"  method="POST" name="frmAbsence" onsubmit="event.preventDefault();">
										<fieldset id="disableFilter" class="disableform" disabled>
											<input type="text" name="frmSubmissionType" id="frmSubmissionType" hidden>
											<div class="row">
												<h4>Resident</h4>
												<p id = "rowOne" class = "errorMsg"></p>
												<div class="col-sm-6">	
													<select name="residentID" id="residentID" required>
														<option value =""> - select - </option>
															<?php
																for ($i = 0; $i < $dropDown_Count; $i++) {  
																echo "<option value='{$resDropdown['row'][$i]['id']}'> {$resDropdown['row'][$i]['ResLName']}, {$resDropdown['row'][$i]['ResFName']}</option>";}
															?>
													</select>
												</div>	
											</div>
											<div class="row row-decreased">
												<div class="col-sm-6">
													<div class="col-sm-2">
														<div class="square">
															<input type="checkbox" name="oneDay" id="oneDay" onclick="singleDay()" value="single" checked>
															<label for="oneDay" class="active"></label>
														</div>
													</div>
													<div class="col-sm-10">
													<p class=checkboxlabel>One Day</p>
												</div>
												</div>
												<div class="col-sm-6">
													<div class="col-sm-2">
														<div class="square">
															<input type="checkbox" name="multiDay" id="multiDay" onclick="multipleDays()" value="multi">
															<label for="multiDay" class="active"></label>
														</div>
													</div>
													<div class="col-sm-10">
													<p class=checkboxlabel>Multiple Days</p>
												</div>												
												</div>
											</div>
											<div class="row">
												<h4 id="multiDates_Title" hidden>Dates of Absence</h4>
												<p id="multiInstructions" hidden>Leaving and Returning date will be considered days that the resident is absent.</p>
											</div>	
											<div class="row">
												<p id = "rowTwo" class = "errorMsg"></p>
												<div class="col-sm-4">
													<label for="formBeginning" name="lblLeaving" id="lblLeaving" hidden>Leaving:</label>
													<label for="formBeginning" name="lblDate" id="lblDate">Date:</label>
													<input type="date" name="formBeginning" id="formBeginning" required>
													<input type="date" name="formOldBeginning" id="formOldBeginning" hidden>
												</div>
												<div class="col-sm-4">
													<label for="formBeginning" name="lblReturning" id="lblReturning" hidden>Returning:</label>
													<input type="date" name="formReturning" id="formReturning" hidden>
													<input type="date" name="formOldReturning" id="formOldReturning" hidden>
												</div>
												<div class="col-sm-4">
													<label for="reasonID">Reason:</label>
														<select name="reasonID" id="reasonID" required>
															<option value =""> - select - </option>
																<?php
																	for ($i = 0; $i < $attendDropdown_Count; $i++) {  
																	echo "<option value='{$attendCodeDropdown['row'][$i]['id']}'> {$attendCodeDropdown['row'][$i]['code']}</option>";}
																?>
														</select>
												</div>											
											</div>
											<input type="hidden" name="userID" id="userID" value="<?php echo $userID;?>"/>
                      <input type="hidden" id="token" name="token" value="<?php echo $token; ?>">
										</fieldset>
											<div class="row">
												<button type="submit" id="submitAbsenceModal" name="submitAbsenceModal" onclick="submitAbsence()">Submit</button>
											</div>
									</form>
								</div>
							</div>
									
						</div>	
          </div>
					<div class="modal-footer">
				</div>
			</div>
			</div>
    </div>

  

	
				<!-- //modal -->  

</body>
<!-- //Body -->
</html>