<!DOCTYPE html>
<?php	
  $token =  md5(uniqid(rand(), true));
  $_SESSION['token'] = $token;
?>

<html lang="en">
<!-- Head -->
<head>
	
	<title>SVLogin</title>
	<!-- Meta-Tags -->
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="keywords" content="Associate a Responsive Web Template, Bootstrap Web Templates, Flat Web Templates, Android Compatible Web Template, Smartphone Compatible Web Template, Free Webdesigns for Nokia, Samsung, LG, Sony Ericsson, Motorola Web Design">
		<meta name="google-signin-client_id" content="1095576072025-g2ctsov7fargrr1ue3vp4hvdbi7qe169.apps.googleusercontent.com">
	
		<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
	<!-- //Meta-Tags -->
	<!-- Custom-Theme-Files -->
	<link defer rel="stylesheet" href="css/bootstrap.min.css" type="text/css" media="all">
	<link rel="stylesheet" href="css/style.css" type="text/css" media="all">
	<link rel="stylesheet" href="css/SunnyViewTheme.css" type="text/css" media="all">
	<link rel="stylesheet" href="css/font-awesome.min.css" />

	<!-- //Custom-Theme-Files -->
	<!-- Web-Fonts -->
	<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800" 	type="text/css">
	<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Montserrat:400,700" 				type="text/css">
	<!-- //Web-Fonts -->
	<!-- Default-JavaScript-File -->
	<script type="text/javascript" src="js/jquery-2.1.4.min.js"></script>
	<script defer type="text/javascript" src="js/bootstrap.min.js"></script>
	<script src="js/main.js"></script>
	<script src="js/BrowserVariation.js"></script>


	
	
	<!--GOOGLE-->
	<script src="https://apis.google.com/js/platform.js" async defer></script>
	<!--FlexSlider-->
		<link defer rel="stylesheet" href="css/flexslider.css" type="text/css" media="screen" />
		<script defer src="js/jquery.flexslider.js"></script>

		<script type="text/javascript">
		$(window).load(function(){
		  $('.flexslider').flexslider({
			animation: "slide",
			start: function(slider){
			  $('body').removeClass('loading');
			}
		  });
		});
	  </script>
	
<!--End-slider-script-->

</head>

<!-- //Head -->
<!-- Body -->
<body>
	<!-- Header -->
	<Header class="headerIndex font">
			<div class="container">
				<figure class="logo" title="Company Logo" aria-label="Sunny View Retirement Community">
					<img src ="images/logo-2.png" alt="Sunny View Retirement Community" >
				</figure>
				<nav class="nav-Header navList-Header" aria-label="Site Navigation">
					<!-- Brand and toggle get grouped for better mobile display -->
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#collapsedNavigation">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<!-- Collect the nav links, forms, and other content for toggling -->
					<div class="collapse navbar-collapse nav-wil" id="collapsedNavigation">
						<ul class="navList">
							<li><a href="#" data-toggle="modal" data-target="#myModal">Login</a></li>
						</ul>
					</div>
				</nav>
					<!-- /navbar-collapse -->
				<div class = "TitlePosition Index-TitlePosition TitleRight">
						<h1 class="TitleSm-view">Associate Login</h1>
						<h1 class="TitleLg-view">Associate</h1>
					</div>
				</div>
		</Header>
	<!-- Content -->
		<section id="indexSection" class="sectionContent">
			<div class="container">
				<div class = "row">
					<div class = "index-col-1">
						<h1>Welcome!</h1>
						<h2>
							Sunny View<br>Web Portal
						</h2>
					</div>
					<div class = "index-col-2">
						<div class = "index-col-2-Container">
							<div class="slider">
								<div class="flexslider">
									<ul class="slides">
										<li class="jsImageCover">
  											<img src="images/SV-facade.jpg" alt="Sunny View">
										</li>
										<li class = "jsImageCover">
  											<img src="images/SV-001.jpg" alt="Sunny View" width="80%">
										</li>
										<li class = "jsImageCover">
											<img src="images/SV-003.jpg" alt="Sunny View" width="80%">
										</li>
										<li class = "jsImageCover">
											<img src="images/SV-004.jpg" alt="Sunny View" width="80%">
										</li>	
										<li class = "jsImageCover">
											<img src="images/SV-005.jpg" alt="Sunny View" width="80%">
										</li>										
									</ul>
								</div>
							</div>
						</div>
					</div>
					<div class="clearfix"> </div>
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
	<!-- Modal Login -->
	<div class=" modal about-modal fade" id="myModal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header"> 
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span class="closeText" aria-hidden="true">&times;</span>
					</button>						
					<h4 class="modal-title">Associate Login</h4>
				</div> 
				<div class="modal-body">
					<div class="agileits-w3layouts-info">
						<img src="images/SV-modal.jpg" class="img-responsive" alt="" />
						<!-- Login Form -->
							<form method="post" action="includes/login_process.php" id="loginform" class="modalInput">
								<div class="login-Row">
									<div class="login-col-1">			
										<label for="username">Username:</label>
									</div>
									<div class="login-col-2">
										<input type="text" name="username" id="username" required/> 
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
                 <input type="hidden" id="token" name="token" value="<?php echo $token; ?>">
								<button type="submit" name="formLogin" >Login</button>
							</form>
							<div class="login-Row password">
								<div class="col-sm-12">
									<a  href="includes/login_process.php?loginError=username">Forgot Username</a>
								</div>
								<div class="col-sm-12">
									<a href="includes/login_process.php?loginError=password" >Forgot Password</a>
								</div>
							</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- //Modal Login-->

</body>
<!-- //Body -->
</html>