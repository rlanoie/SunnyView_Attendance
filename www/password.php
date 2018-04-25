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
		require '../includes/commonMsg.php';
		if (session_status() !== PHP_SESSION_ACTIVE) {sec_session_start();}//start the session

$tempPassword = '';
	$password = $_SESSION['postpassword'];
  
  $userID = $_SESSION['user']['id'];
  $token =  $_SESSION['token'];
	pageAccess($token, $userID, $db);
  $username = $_SESSION['user']['username'];
	

	if($_SESSION['user']['tempPassword'] == 1){
	  $tempPassword = true;
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

	<title>Associate Login</title>
	<!-- Meta-Tags -->
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
	<script>
		


	function passwordMsg(){
		var temporaryPassword = '<?php echo $tempPassword?>';
		if(temporaryPassword == 1){
			document.getElementById("errorMsg").innerHTML = "You must change your temporary Password before proceeding."	
      document.getElementById("passwordType").innerHTML = "Temporary Password:"	
		}		
	}
	</script>
</head>
<!-- //Head -->
<!-- Body -->
<body onload="passwordMsg()">
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
										<li style="padding: 0;"><a href="settings.php">Settings</a></li>
										<li style="padding: 0;"><a href="logout">Logout</a></li>
									</ul>
							</li>
							<li class="dropdown" style="padding: 0;">
								<ul class ="dropdown-menu-button">
									<li style="padding: 0;"><a href="settings.php">Settings</a></li>
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
	<!-- //Header -->
	

	<!-- Section -->
	<section id="passwordSection" class="sectionContent modalPage">
		<div class="container">
			<div id="section">
				<div class="modal-dialog" role="document">
					<div class="login-content">
					<div class="login-header"> 
						<h4  class="modal-title">Associate Login</h4>
					</div> 
						
					<div class="login-body">
          	<p id="errorMsg" class="loginError"></p>
						<!-- Login Form -->
							<form id="loginform" method="post" id="loginform" class="modalInput" onsubmit="event.preventDefault();">
								<div class="row login-Row">
									<div class="col-sm-6">			
										<label for="oldPassword" id="passwordType">Old Password:</label>
									</div>
									<div class="col-sm-5">
										<input type="password" name="oldPassword" id="oldPassword" required/> 
									</div>
								</div>
								<div class="row">
									<div class="col-sm-6">
										<label for="newPassword">New Password:</label>
									</div>
									<div class="col-sm-5">
										<input type="password" name="newPassword" id="newPassword" required/> 
									</div>
								</div>
								<br>
								<div class="row">
									<div class="col-sm-6">
										<label for="passwordVerify">Verify Password:</label>
									</div>
									<div class="col-sm-5">
										<input type="password" name="passwordVerify" id="passwordVerify" required/> 
									</div>
								</div>
								<div class="row">
									<div class="col-sm-6">
									</div>
									<div class ="col-md-5 alignRight">
                    <input type="hidden" id="token" name="token" value="<?php echo $token; ?>">
										<button type="submit" id="passwordUpdate" name="passwordUpdate" onclick="changePassword('<?php echo $username?>', <?php echo $userID?>)">Submit</button>	
									</div>
									
								</div>
							</form>	
						<!-- //Login Form -->
					
				</div>
			</div>
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
</body>
<!-- //Body -->
</html>