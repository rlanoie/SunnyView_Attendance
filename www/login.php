<!--
	Author: W3layouts
	Author URL: http://w3layouts.com
	License: Creative Commons Attribution 3.0 Unported
	License URL: http://creativecommons.org/licenses/by/3.0/
-->
<!DOCTYPE html>
 	<?php	
		//require '../includes/login_process.php';
include_once '../includes/session.php';
require '../includes/commonMsg.php';
include_once '../includes/function.php';
if (session_status() !== PHP_SESSION_ACTIVE) {sec_session_start();}

if(isset($_GET["login"])){
	$loginError=test_input($_GET["login"]);	
}else{
	$loginError='';
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
  <script src="../js/login.js"></script>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script>
		$( document ).ready(function() {
    	console.log( "ready!" );
			var pageFunction = "<?php echo $loginError ?>";
			
			switch(pageFunction){
				case 'password':
					$( "#forgotPassword" ).trigger( "click" );	
					break;
				case 'username':
					$( "#forgotUsername" ).trigger( "click" );
					break;
				case 'error3':
					var errorMessage = 'To many login attempts!  Your account has been locked. You must reset your password. ';
					document.getElementById("loginMessage").innerHTML = errorMessage;
					var hyperlink = $('<a>', {'href':'#', 'class':'passwordReset', 'html':' Password Reset'}).appendTo('#loginMessage');
					hyperlink.on("click", function(event){resetPasswordOnClick();});
					
					break;
				case 'error1':
					var errorMessage = 'LOGIN ERROR: \nYou have entered an incorrect username or password.' ;
					document.getElementById("loginMessage").innerHTML = errorMessage;
					break;
				default:
					var errorMessage = 'Enter your username and password.';
					document.getElementById("loginMessage").innerHTML = errorMessage;
					break;
			}
			
		});
	</script>
</head>
<!-- //Head -->
<!-- Body -->
<body>
	<!-- Header -->
	<Header class="headerIndex font">
			<div class="container">
  			<!--<figure class="logo" title="Company Logo" aria-label="Sunny View Retirement Community">-->
					<img src ="../images/logo-2.png" alt="Sunny View Retirement Community" >
				<!--</figure>-->
					<div class = "TitlePosition TitleLeft">

					</div>
			</div>
	</header>
	<!-- //Header -->
	

	<!-- Section -->
	<section id="indexSection" class="SectionContent">
		<div class="container">
	<div>
		<div class="modal-dialog" role="document">
			<div class="login-content">
				<div class="login-header"> 
					<h4 class="modal-title">Associate Login</h4>
				</div> 
				<div class="login-body">
          <p id="loginMessage" class="loginError"></p>

					<div class="agileits-w3layouts-info">
						<!-- Login Form -->
							<form id="frm_loginform" method="post" action="../includes/login_process.php" class="modalInput loginform" autocomplete="off">
								<div class="login-Row">
									<div class="login-col-1">			
										<label for="username">Username:</label>
									</div>
									<div class="login-col-2">
										<input type="text" name="username" id="username1" required/> 
									</div>
								</div>
								<div class="login-Row">
									<div class="login-col-1">
										<label for="password">Password:</label>
									</div>
									<div class="login-col-2">
										<input type="password" name="password" id="password" required autocomplete="off"/> 
									</div>
								</div>
								<button type="submit" name="formLogin" >Login</button>
							</form>
						
						<!--Reset Password-->
							<form id="frm_resetPassword" method="post" class="modalInput loginform" onsubmit="event.preventDefault();" disabled hidden>
								<div class="login-Row">
									<div class="login-col-1">			
										<label for="username2">Username:</label>
									</div>
									<div class="login-col-2">
										<input type="text" name="username2" id="username2" disabled required/> 
									</div>
								</div>
								<div class="login-Row">
									<div class="login-col-1">
										<label for="email2">Email:</label>
									</div>
									<div class="login-col-2">
										<input type="email" name="email2" id="email2" disabled required/> 
									</div>
								</div>
								<button type="submit" name="passwordReset" id="passwordReset" onclick="resetPassword('forgot')"  >Submit</button>
								<button type="submit" name="lockedPassword" id="lockedPassword" onclick="resetPassword('locked')"  >Submit</button>
							</form>
						
						<!--Get Username-->
							<form id="frm_resetUsername" method="post" class="modalInput loginform" onsubmit="event.preventDefault();" disabled hidden>
								<div class="login-Row">
									<div class="login-col-1">			
										<label for="email3">Email:</label>
									</div>
									<div class="login-col-2">
										<input type="email" name="email3" id="email3" disabled required/> 
									</div>
								</div>
								<button type="submit" name="usernameFind" id="usernameFind" onclick="findUsername()">Submit</button>
							</form>						
							<div class="login-Row password">
								<div class="col-sm-12">
									<a  id="forgotUsername">Forgot Username</a>
								</div>
								<div class="col-sm-12">
									<a id="forgotPassword">Forgot Password</a>
								</div>
							</div>
						<!-- //Login Form -->
					</div>
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