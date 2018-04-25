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
		include_once '../includes/userChanges.php';
		if (session_status() !== PHP_SESSION_ACTIVE) {sec_session_start();} //start the session
		// check if user has logged in.  If not redirect to index page
    $userID = $_SESSION['user']['id'];
    $token =  $_SESSION['token'];
		pageAccess($token, $userID, $db);

    $username = $_SESSION['user']['username'];
	  

		$permission = $_SESSION['permissions']['users'];
		if($permission == 'none'){
			header('location:dashboard.php');
		}else{
			$queryResults = getEmployeesActive(1, $db);
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

	<title>Employee Page</title>
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
	<script src="../js/ajax.js"></script>
<script src="../js/employee.js"></script>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script>
		$(document).ready(function(){
			getUserPermission('employee', permission()); //check what permissions user has for this page
			//check user permission settings
			
			$(".dropdown").click(function() {  
						if($(this).hasClass("open")===true){
							$(this).removeClass("open"); 	 	
						}else{
							$(this).addClass("open");
						}
				});
		
			function permission(){
				return("<?php echo $_SESSION['permissions']['users'] ?>");
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
										<li style="padding: 0;"><a href="admin-page">Admin Page</a></li>                    
										<li style="padding: 0;"><a href="../dashboard">Dashboard</a></li>                    
										<li style="padding: 0;"><a href="logout">Logout</a></li>
									</ul>
							</li>
							<li class="dropdown" style="padding: 0;">
								<ul class ="dropdown-menu-button">
									<li><a class="navHidden" href="admin-page">Admin Page</a></li>
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
	  <section id = "sectionBody" class="sectionContent theme">
			<div class="container">
				<Header>
					<h1 class="header">Employees</h1>
				</Header>
					<div id = "employeeBody" class="bodyContent">
						<!--ASIDE-->
						<aside>
							<header>
								<ul style="list-style-type:none">
									<a href="" data-toggle="modal" class="addModalEmp">Add Employee</a>
								</ul>
								<br>
								<?php
										echo "<h4>Showing results for ". $queryResults['count']." record(s).</h4>";
								?>
							</header>
													
							<section>
								<form method="POST" name="filterEmployees" id="filterEmployees" autocomplete="off" onsubmit="event.preventDefault();">
									<fieldset id="disableFilter">
										<div class="row">
											<div class="col-sm-12">
												<label  for="formFilterID" class="formlabel">Employee ID:</label> 
												<input type="text" name="formFilterID" id="formFilterID" value="<?php echo($frmID)?>"/> 
						  			    <div data-role="fieldcontain">
				  		    			  <fieldset data-role="controlgroup">
    		  				    			<input type="radio" name="formFilterID_radio" id="formFilterID_radio_0" value="=" <?php echo ($frmIDRadio=='=')?'checked':'' ?>>
  	    	    							<label for="radio3_0" class="radiolbl">Match</label>
	        	  							<input type="radio" name="formFilterID_radio" id="formFilterID_radio_1" value=" LIKE " <?php echo ($frmIDRadio==' LIKE ')?'checked':'' ?>/>
														<label for="radio3_1" class="radiolbl">Similar</label>
    		    							</fieldset>
      									</div>
											</div>
											<div class="col-sm-12">
												<label  for="formLastName" class="formlabel">Last Name:</label> 
												<input type="text" name="formLastName" id="formLastName" value="<?php echo($frmEmpLName)?>"/> 
												<div data-role="fieldcontain">
	    						  			<fieldset data-role="controlgroup">
  		  	  				  	  	<input type="radio" name="formLastName_radio" id="formLastName_radio_0" value="=" <?php echo ($frmEmpLRadio=='=')?'checked':'' ?>/>
  	  	  	  				  		<label for="formLastName_radio_0" class="radiolbl">Match</label>
	      	  	  						<input type="radio" name="formLastName_radio" id="formLastName_radio_1" value=" LIKE " <?php echo ($frmEmpLRadio==' LIKE ')?'checked':'' ?>/>
        	  								<label for="formLastName_radio_1" class="radiolbl">Similar</label>
		        							</fieldset>
	  		    						</div>
											</div>
											<div class="col-sm-12">
												<label  for="formFirstName" class="formlabel">First Name:</label> 
												<input type="text" name="formFirstName" id="formFirstName" value="<?php echo($frmEmpFName)?>"/> 
									      <div data-role="fieldcontain">
      									  <fieldset data-role="controlgroup">
        			  						<input type="radio" name="formFirstName_radio" id="formFirstName_radio_0" value="=" <?php echo ($frmEmpFRadio=='=')?'checked':'' ?>>
          									<label for="formFirstName_radio_0" class="radiolbl">Match</label>
	          								<input type="radio" name="formFirstName_radio" id="formFirstName_radio_1" value=" LIKE " <?php echo ($frmEmpFRadio==' LIKE ')?'checked':'' ?>/>
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
												</fieldset>
											</div>
										</div>
											<div class="clearfix"> </div>
										</div>
										<div class="team-row">
											<div class="col-md-12 centerContent">
												<button type="submit" name="employeeFilter" id="employeeFilter" aria-label="Filter">Filter</button>	
											</div>
											<div class="clearfix"> </div>
										</div>
									</fieldset>
								</form>
							</section>
							<section>
								<ul style="list-style-type:none">
									<a href=# onclick="printByTag('sectionBody');">Save To PDF</a>
								</ul>
							</section>
							<footer>
							</footer>
						</aside>  
<!--/ASIDE-->
						<div id="grid" class="body-container">
							<header>	
								<div id="gridlist-header" class='team-row sectionResults'>
									<div class = "row text"> 
										<div class = "col-sm-2">No.</div>  
										<div class = "col-sm-3">ID</div>                  
										<div class = "col-sm-3">Last Name</div>
										<div class = "col-sm-3">First Name</div>
									</div>
								</div>
							</header> 
							<section class = "gridcontainer">
								<div id='gridlist' class='team-row sectionResults'>
								<?php
									if ($queryResults['count'] > 0) {
										$count=0;
  	                foreach($queryResults ['row'] as $column) {
											$count++;
											echo "<a  class='linkClick'  data-toggle='modal'>";
												echo"<div class='row rowResults selectEmployee rowEmpID_".$column['id']."'>"; 
													echo"<div class='col-sm-2 row_count'>". $count . "</div>";
													echo"<div class='col-sm-3 row_ID'>". $column['id'] . "</div>";
													echo"<div class='col-sm-3 row_nameLast'>" . $column['userLastName'] . "</div>";
													echo"<div class='col-sm-3 row_nameFirst'>" . $column['userFirstName'] . "</div>";                  
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
    <div class="modal theme fade" id="modal_User" role="dialog" >
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h1 class="modal-title">Employee Information</h1>
					</div>
					<div class="modal-body">
						<div class="modal-container">
							<div id="modal-bodyContent" class = "body">
						<!--Displays the user's current information-->	
								<div id="modal_Row_headerDetails" class="row">
									<div class="col-sm-8">
										<h4>Current Information</h4>
										<p id="Modal_CurrResult"></p>
									</div>
									<div class="col-sm-3" id = "colDeleteUser">
    								<button type="submit" id="deactivateEmp" name="deactivateEmp" aria-label="Delete User">Delete User</button>
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
								<h2 id="modal-ChangesTitle">Employee Changes</h2>
								<h2 id="modal-AddTitle">Add Employee</h2>
								<div class="formContent">
                  <p id="addMessage"></p>
									<form id="formUserChange" name="formUserChange" method="POST" autocomplete="off">	
										<fieldset id="disableUserChange" class="disableform" >	
											<div class="row" hidden>
												<div class="col-sm-6" hidden>
													<input type="hidden" class="form-control" name="frm_id" id="frm_id">
                          <input type="hidden" class="form-control" name="userID" id="userID" value=<?php echo $userID ?>>
                          <input type="hidden" id="token" name="token" value="<?php echo $token; ?>">
												</div>
											</div>
<!--row-->					<!--New employee information-->
										<div class="row">
											<p id = "rowOne" class = "errorMsg"></p>
											<div class="col-sm-6">
												<label for="newFirst">First Name:</label>
												<input type="text" name = "newFirst" id="newFirst" >													
											</div>
											<div class="col-sm-6">
												<label for="newLast">Last Name:</label>
												<input type="text" name="newLast" id="newLast" >													
											</div>												
										</div>	
										<div class="row">
											<p id = "rowTwo" class = "errorMsg"></p>
											<div class="col-sm-6">
												<label for="newUsername">Username:</label>
												<input type="text" name="newUsername" id="newUsername" autocomplete="false" >													
											</div>												
											<div class="col-sm-6">											
												<label for="newPassword">Temporary Password:</label>
												<input type="password" name="newPassword" id="newPassword" autocomplete="new-password" minlength="8" maxlength="24">													
											</div>
										</div>
										<div class="row">
											<p id = "rowThree" class = "errorMsg"></p>
											<div class="col-sm-6">
												<label for="newEmail">Email:</label>
												<input type="email" name="newEmail" id="newEmail" autocomplete="new-email" >													
											</div>
										</div>
<!--USER PERMISSIONS-->
										<div class="row">
											<div class="col-sm-12">											
											<h3>Employee Permissions:<h3>
										</div>													
										</div>											
										<div class="container">											
							<!--Attendance-->	
											<h4>Attendance</h4>
											<div class="row">
												<div class="col-sm-6">													
													<div class="col-sm-2">
														<div class="square">
															<input type="checkbox" name="attendance" id="attendance"  value="write">
															<label for="attendance" class="active"></label>
														</div>
													</div>
													<div class="col-sm-10">
														<p class=checkboxlabel>Take Attendance</p>
													</div>
												</div>
											</div>
							<!--Residents-->
											<h4>Residents</h4>
											<div class="row">
												<div class="col-sm-6">												
													<div class="col-sm-2">
														<div class="square">
															<input type="checkbox" name="readResident" id="readResident"  value="read" onclick="residentPermissions('read')">
															<label for="readResident" id="labelRResident" class="checkable" ></label>
														</div>
													</div>
													<div class="col-sm-10">
														<p class=checkboxlabel>View Resident List</p>
													</div>
												</div>
												<div class="col-sm-6">
											  	<div class="col-sm-2">
														<div class="square">
															<input type="checkbox" name="writeResidents" id="writeResidents"  value="write" onclick="residentPermissions('write')">
															<label for="writeResidents" id="labelWResident" class="checkable"></label>
														</div>
													</div>
													<div class="col-sm-10">
														<p class=checkboxlabel>Make Resident changes</p>
													</div>
												</div>
											</div>
							<!--Users-->
											<div class="row no-col-padding">
												<div class="col-sm-1">
													<div class="square">
														<input type="checkbox" name="writeadmin" id="writeadmin" onclick="write_AdminClick('writeadmin', '#fieldsetAdminChecks')"  value="write">
														<label for="writeadmin" id="labelWAdmin" class="active"></label>
													</div>
												</div>
												<div class="col-sm-10">												
													<h4>Admin Privileges</h4>
												</div>
											</div>
												<fieldset id="fieldsetAdminChecks" class="adminPriv">
													<div class="row">
														<div class="col-sm-6">
															<div class="col-sm-2">
																<div class="square">
																	<input type="checkbox" name="readUsers" id="readUsers"  value="read" onclick="employeePermissions('read')">
																	<label for="readUsers" id="labelRUsers" class="checkable"></label>
																</div>
															</div>
															<div class="col-sm-10">
																<p class=checkboxlabel>View Employees</p>
															</div>												
														</div>
														<div class="col-sm-6">
														  <div class="col-sm-2">
																<div class="square">
																	<input type="checkbox" name="writeUsers" id="writeUsers"  value="write" onclick="employeePermissions('write')">
																	<label for="writeUsers" id="labelWUsers" class="checkable"></label>
																</div>
															</div>
												  		<div class="col-sm-10">
																<p class=checkboxlabel>Make Employee Changes</p>
															</div>
														</div>
													</div>								
												</fieldset>
											<div class="row">
												<button type="submit" id="submitModal_UpdateEmp" name="submitModal_UpdateEmp"  aria-label="Submit Changes" >Submit</button>															
												<button type="submit" id="submitModal_AddEmp" name="submitModal_AddEmp" aria-label="Add Employee">Add</button>															
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