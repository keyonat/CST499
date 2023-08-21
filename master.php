<?php
error_reporting(E_ALL ^ E_NOTICE);
// var_dump($_SESSION);
if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 
?>
<html lang='en'>
<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1'/>
	<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css'>
	<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css'>
	<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js'>
</head>
<body>
</div>
<nav class='navbar navbar-default'>
	<div class='container-fluid'>
		<div class='navbar-header'>
			<button type='button' class='navbar-toggle' data-toggle='collapse' data-target='#Navbar'>
				<ul class="navbar-nav justify-content-center">
				<span class='icon-bar'></span>
				<span class='icon-bar'></span>
				<span class='icon-bar'></span>
			</button>
		</div>
		<?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) { ?>
		<div class='collapse navbar-collapse' id='Navbar'>
			<ul class="container text-center">
				<li class='active'><a href="index.php"><span class='glyphicon glyphicon-home'></span> Home</a></li>
				<li class='active'><a href="profile.php"><span class='glyphicon glyphicon-pencil'></span> Profile</a></li>
				<li class='active'><a href="studentregadvanced.php"><span class='glyphicon glyphicon-plus'></span> Course Registration</a></li>
				<?php if (isset($_SESSION['userType']) && $_SESSION['userType'] = 'Administrator') { ?>
				<li class='active'><a href="courseRegistration.php"><span class='glyphicon glyphicon-th-list'></span> Edit Course Catalog</a></li>
				<?php } ?>
				<li class='active'><a href="ContactUs.php"><span class='glyphicon glyphicon-envelope'></span> Contact Us</a></li>
				<li class='active'><a href="logout.php"><span class='glyphicon glyphicon-user'></span> Logout</a></li>
			</ul>
		</div>
		<?php } else { ?>
		<div class='collapse navbar-collapse' id='Navbar'>
			<ul class="nav navbar-nav navbar-default">
				<li class='active'><a href="index.php"><span class='glyphicon glyphicon-home'></span> Home</a></li>
				<li class='active'><a href="Registration.php"><span class='glyphicon glyphicon-pencil'></span> Register</a></li>
				<li class='active'><a href="Login.php"><span class='glyphicon glyphicon-user'></span> Login</a></li>
				<li class='active'><a href="ContactUs.php"><span class='glyphicon glyphicon-envelope'></span> Contact Us</a></li>
			</ul>
		</div>
		<?php } ?>
	</div>
</nav>
</body>
</html>
