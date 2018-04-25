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
		require '../includes/residentFunctions.php';
		if (session_status() !== PHP_SESSION_ACTIVE) {sec_session_start();}

// check if user has logged in.  If not redirect to index page
	//$password = $_SESSION['postpassword'];
  $userID = $_SESSION['user']['id'];
  $token =  $_SESSION['token'];
  pageAccess($token, $userID, $db);

  $username = $_SESSION['user']['username'];
	

	
		$permission = $_SESSION['permissions']['residents'];
		if($_SESSION['permissions']['residents'] =='none'){
			header('location:dashboard.php');
		}else{
			$queryResults = getActiveResidents(1, $db);
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

		<title>Residents</title>
		<!-- Meta-Tawgs -->
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="keywords" content="Associate a Responsive Web Template, Bootstrap Web Templates, Flat Web Templates, Android Compatible Web Template, Smartphone Compatible Web Template, Free Webdesigns for Nokia, Samsung, LG, Sony Ericsson, Motorola Web Design">
		<script type="application/x-javascript">
			addEventListener("load", function() {setTimeout(hideURLbar, 0);}, false);

			function hideURLbar() {
				window.scrollTo(0, 1);
			}
		</script>
		<link rel="stylesheet" type="text/css" href="../css/InisopeDirectory.css" />
		<!-- //Meta-Tags -->
		<!-- Custom-Theme-Files -->
		<link rel="stylesheet" href="../css/bootstrap.min.css" type="text/css" media="all">
		<link rel="stylesheet" href="../css/style.css" type="text/css" media="all">
		<link rel="stylesheet" href="../css/SunnyViewTheme.css" type="text/css" media="all">
		<link rel="stylesheet" href="../css/font-awesome.min.css" />

		<!-- //Custom-Theme-Files -->
		<!-- Web-Fonts -->
		<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800" type="text/css">
		<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Montserrat:400,700" type="text/css">
		<!-- //Web-Fonts -->
		<!-- Default-JavaScript-File -->
		<script type="text/javascript" src="../js/jquery-2.1.4.min.js"></script>
		<!--<script type="text/javascript" src="../js/bootstrap.min.js"></script>-->

		<script src="../js/main.js"></script>
		<script src="../js/ajax.js"></script>
		<script src="../js/resident.js"></script>

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


		<script>
			$(document).ready(function() {
				getUserPermission('resident', permission()); //check what permissions user has for this page
				//check user permission settings
			
				function permission() {
					return ("<?php echo $_SESSION['permissions']['residents'] ?>");
				}
			});
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
								<ul class="dropdown-menu-button">
									<li><a class="navHidden" href="dashboard">Dashboard</a></li>
									<li><a class="navHidden" href="logout">Logout</a></li>
								</ul>
							</li>
						</ul>
					</div>
				</nav>
				<div class="TitlePosition TitleLeft">
					<h1 class="TitleSm-view">
						<?php echo"$username"; ?>
					</h1>
					<h1 class="TitleLg-view">
						<?php echo"$username"; ?>
					</h1>
				</div>
			</div>
		</Header>
		<!-- Section -->
		<section id="sectionBody" class="sectionContent theme">
			<div class="container">
				<Header>
					<h1 class="header">Residents</h1>
				</Header>
				<div id="ressidentBody" class="bodyContent">
					<!--ASIDE-->
					<aside>
						<header>
							<ul style="list-style-type:none">
								<a href="" data-toggle="modal" class="modalOpen_AddResident" id="modalOpen_AddResident" aria-label="Add Resident">Add Resident</a>
							</ul>
							<h5 id="qryResults"><?php	echo "Showing results for <br/>".$queryResults['count']." record(s).";?></h5>
						</header>
						<section>
							<h4>
								Search
							</h4>
							<form method="POST" name="filterResidents" id="filterResidents" autocomplete="off" onsubmit="event.preventDefault();">
								<fieldset id="disableFilter">
									<div class="row">
										<div class="col-sm-12" hidden>
											<label for="formFilterID" class="formlabel">Resident ID</label>
											<input type="text" name="formFilterID" id="formFilterID" value="<?php echo($frmID)?>" />
											<div data-role="fieldcontain">
												<fieldset data-role="controlgroup">
													<input type="radio" name="formFilterID_radio" id="formFilterID_radio_0" value="=" <?php echo ($frmIDRadio=='=' )? 'checked': '' ?>>
													<label for="formFilterID_radio_0" class="radiolbl">Match</label>
													<input type="radio" name="formFilterID_radio" id="formFilterID_radio_1" value=" LIKE " <?php echo ($frmIDRadio==' LIKE ' )? 'checked': '' ?>/>
													<label for="formFilterID_radio_1" class="radiolbl">Similar</label>
												</fieldset>
											</div>
										</div>
										<div class="col-sm-12">
											<label for="formFilterHID" class="formlabel">Key Fob #</label>
											<input type="password" name="formFilterHID" id="formFilterHID" autocomplete="new-password"/>
											<div data-role="fieldcontain">
												<fieldset data-role="controlgroup">
													<input type="radio" name="formFilterHID_radio" id="formFilterHID_radio_0" value="=" <?php echo ($frmHIDRadio=='=' )? 'checked': '' ?>>
													<label for="formFilterHID_radio_0" class="radiolbl">Match</label>
													<input type="radio" name="formFilterHID_radio" id="formFilterHID_radio_1" value=" LIKE " <?php echo ($frmHIDRadio==' LIKE ' )? 'checked': '' ?>/>
													<label for="formFilterHID_radio_1" class="radiolbl">Similar</label>
												</fieldset>
											</div>
										</div>
										<div class="col-sm-12">
											<label for="formLastName" class="formlabel">Last Name:</label>
											<input type="text" name="formLastName" id="formLastName" value="<?php echo $frmResLName?>" />
											<div data-role="fieldcontain">
												<fieldset data-role="controlgroup">
													<input type="radio" name="formLastName_radio" id="formLastName_radio_0" value="=" <?php echo ($frmLRadio=='=' )? 'checked': '' ?>/>
													<label for="formLastName_radio_0" class="radiolbl">Match</label>
													<input type="radio" name="formLastName_radio" id="formLastName_radio_1" value=" LIKE " <?php echo ($frmLRadio==' LIKE ' )? 'checked': '' ?>/>
													<label for="formLastName_radio_1" class="radiolbl">Similar</label>
												</fieldset>
											</div>
										</div>
										<div class="col-sm-12">
											<label for="formFirstName" class="formlabel">First Name</label>
											<input type="text" name="formFirstName" id="formFirstName" value="<?php echo $frmResFName?>" />
											<div data-role="fieldcontain">
												<fieldset data-role="controlgroup">
													<input type="radio" name="formFirstName_radio" id="formFirstName_radio_0" value="=" <?php echo ($frmFRadio=='=' )? 'checked': '' ?>>
													<label for="formFirstName_radio_0" class="radiolbl">Match</label>
													<input type="radio" name="formFirstName_radio" id="formFirstName_radio_1" value=" LIKE " <?php echo ($frmFRadio==' LIKE ' )? 'checked': '' ?>/>
													<label for="formFirstName_radio_1" class="radiolbl">Similar</label>
												</fieldset>
											</div>
										</div>
										<div id="include" class="col-sm-6">
											<label for="" class="formlabel">Include:</label>
											<div data-role="fieldcontain">
												<fieldset data-role="controlgroup">
													<div class="col-sm-12">
														<input type="radio" name="active" id="active_radio_2" value="" <?php echo ($frmActiveRadio=='' )? 'checked': '' ?>/>
														<label for="formActive_radio_2" class="radiolbl">All</label>														
													</div>
													<div class="col-sm-12">
														<input type="radio" name="active" id="active_radio_1" value="1" <?php echo ($frmActiveRadio=='1' )? 'checked': '' ?>>
														<label for="formActive_radio_1" class="radiolbl">Active Only</label><br/>
													</div>
													<div class="col-sm-12">
														<input type="radio" name="active" id="active_radio_0" value="0" <?php echo ($frmActiveRadio=='0' )? 'checked': '' ?>>
														<label for="formActive_radio_0" class="radiolbl">Inactive Only</label>
													</div>
												</fieldset>
											</div>
										</div>
										<div id="sort" class="col-sm-6">
											<label for="" class="formlabel ">Sort:</label>
											<div data-role="fieldcontain">
												<fieldset data-role="controlgroup">
													<div class="col-sm-12">
														<input type="radio" name="frmSort" id="frmSort_radio_0" value="0" <?php echo ($frmSortRadio=='0' )? 'checked': '' ?>/>
														<label for="frmSort_radio_0" class="radiolbl">A-Z</label>														
													</div>
													<div class="col-sm-12">
														<input type="radio" name="frmSort" id="frmSort_radio_1" value="1" <?php echo ($frmSortRadio=='1' )? 'checked': '' ?>>
														<label for="frmSort_radio_1" class="radiolbl">Z-A</label><br/>
													</div>
													<div class="col-sm-12">
														<input type="radio" name="frmSort" id="frmSort_radio_2" value="2" <?php echo ($frmSortRadio=='2' )? 'checked': '' ?>>
														<label for="frmSort_radio_2" class="radiolbl">Room #</label>
													</div>
												</fieldset>
											</div>
										</div>
										<div class="clearfix"> </div>
									</div>
									<div class="team-row">
										<div class="col-md-12 centerContent">
											<button type="submit" name="residentfilter" id="residentfilter" aria-label="Filter">Filter</button>
										</div>
										<div class="clearfix"> </div>
									</div>
								</fieldset>
							</form>
						</section>
						<section>
							<ul style="list-style-type:none">
								<a href=# onclick="printByTag('sectionBody');">Print Report</a>
							</ul>
						</section>
						<footer>
						</footer>
					</aside>
					<!--/ASIDE-->
					<div id="grid" class="body-container">
						<header>
							<div id="gridlist-header" class='team-row sectionResults'>
								<div class="row text">
									<div class="col-sm-2" hidden>ID</div>
									<div class="col-sm-2 ">No.</div>
									<div class="col-sm-3">Last Name</div>
									<div class="col-sm-3">First Name</div>
									<div class="col-sm-2">Room #</div>
								</div>
							</div>
						</header>
						<section class="gridcontainer">
							<div id='gridlist' class='team-row sectionResults'>
								<?php
									if ($queryResults['count']  > 0) {
										$count=0;
  	                foreach($queryResults ['row'] as $column) {
											echo "<a class=''  data-toggle='modal'>";
												if($column['active'] == 0){
													echo"<div id= 'rowResID_".$column['id']."' class='row rowResults inactive selectResident'>"; 
												}else{
													echo"<div id= 'rowResID_".$column['id']."' class='row rowResults selectResident'>"; 
												}
													$count++;
													echo"<div class='col-sm-2 rowCount'>". $count . "</div>";
													echo"<div hidden class='col-sm-2 rowId'>". $column['id'] . "</div>";
													echo"<div class='col-sm-3 row_nameLast'>" . $column['ResLName'] . "</div>";
													echo"<div class='col-sm-3 row_nameFirst'>" . $column['ResFName'] . "</div>";                  
													echo"<div class='col-sm-2 row_RoomNumber'>" . $column['roomNumber'] . "</div>";
													
												echo"</div>";
											echo"</a>";
    								}
									}
								?>
							</div>
						</section>
						<footer></footer>
					</div>
				</div>
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
		<!-- modal Employee Details -->
		<div class="modal theme fade" id="modalResident" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h1 class="modal-title">Resident</h1>
					</div>
					<div class="modal-body">
						<div class="modal-container">
							<div id="modal-bodyContent" class="body">
								<!--Displays the resident's current information-->
								<div id="modal_Row_headerDetails" class="row">
									<div class="col-sm-8">
										<h4>Current Information</h4>
										<p id="Modal_CurrResult"></p>
									</div>
									<div class="col-sm-3 disabled" id="colDeleteRes">
										<button type="submit" id="modalbtn_deactivateRes" name="modalbtn_deactivateRes" aria-label="Delete">Delete</button>
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
								<h2 id="modal-ChangesTitle">Resident Changes</h2>
								<h2 id="modal-AddTitle">Add Resident</h2>
								<div class="formContent">
									<form id="formUserChange" name="formUserChange" method="POST" autocomplete="off" onsubmit="event.preventDefault();">

										<fieldset id="disableResidentChange" class="disableform" disabled>
											<div class="row" hidden>
												<div class="col-sm-6">
													<input type="hidden" class="form-control" name="frm_id" id="frm_id">
													<input type="hidden" class="form-control" name="userID" id="userID" value=<?php echo $userID ?>>
                          <input type="hidden" id="token" name="token" value="<?php echo $token; ?>">
												</div>
											</div>
											<!--row-->
											<!--New employee information-->
											<div class="row">
												<p id="rowOne" class="errorMsg"></p>
												<div class="col-sm-6">
													<label for="newFirst">First Name:</label>
													<input type="text" name="newFirst" id="newFirst">
												</div>
												<div class="col-sm-6">
													<label for="newLast">Last Name:</label>
													<input type="text" name="newLast" id="newLast" autocomplete="new-LastName">
												</div>
											</div>
											<div class="row">
												<input type="hidden">
												<p id="rowTwo" class="errorMsg"></p>
												<div class="col-sm-6">
													<label for="newRFID">Key Fob #:</label>
													<input type="password" name="newRFID" id="newRFID" autocomplete="new-password">
												</div>
												<div class="col-sm-6">
													<label for="newRoom">Room #:</label>
													<input type="text" name="newRoom" id="newRoom" autocomplete="false">
												</div>
											</div>
											<div class="container">
												<div class="row">
													<button type="submit" id="modalBtn_UpdateResident" name="modalBtn_UpdateResident" aria-label="Submit">Submit</button>
													<button type="submit" id="modalBtn_AddResident" name="modalbtn_AddResident" aria-label="Add">Add</button>
												</div>
											</div>
										</fieldset>
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