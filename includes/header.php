<?php #temporary header info ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Kimball Music</title>
<link type="text/css" rel="stylesheet" media="all" href="/css/style.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script type="text/javascript" src="/js/main.js"></script>
<title>Kimball Music</title>
<link rel="stylesheet" type="text/css" href="/css/style.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script type="text/javascript" src="/js/main.js"></script>
<title>Kimball Music</title>
<link type="text/css" rel="stylesheet" media="all" href="../css/style.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script type="text/javascript" src="/js/main.js"></script>
</head>
<body>
	<div id="wrapper">
		<div id="upper">
			<div id="phonenumber">
				Questions? Call us at 800-555-1234
			</div>
			
			<div id="login">
				<ul>
					<li>
						<?php
							if(isset($_SESSION['firstname'])){
								echo 'Welcome, '. $_SESSION['firstname'] .'! | ';
								echo '<a href="/login/logout.php">Log Out</a>';
							}
							else {
								echo '<a href="/login/index.php">Log In</a></li>';
								echo '<li>|</li>';
								echo '<li><a href="/register">Sign Up</a>';
							}
						?>
					</li>
					<li>|</li>
					<li><a href="#">My Cart (0)</a></li>
				</ul>
			</div>
		</div>
		<div id="header">
			<div id="title">
				<a href="/">Kimball Music</a>
			</div>
			
			<div id="search">
			
				<form action="#">
				
					<fieldset id="searchbox">
						<input type="text" name="search" value="" />
					</fieldset>
					
					<fieldset id="searchbutton">
						<input type="submit" value="Search" name="submit" class="submit" />		
					</fieldset>

				</form>
			
			</div>
		</div>
		
		<div id="navigation">
			<div id="links">
				<ul>
					<li><a href="#">Violin</a></li>
					<li><a href="#">Viola</a></li>
					<li><a href="#">Cello</a></li>
					<li><a href="#">Bass</a></li>
					<li><a href="#">Accessories</a></li>
				</ul>
			</div>
			
			<div id="social">
			</div>
		</div>
		
		<div id="main">
		<?php /**
<form action="../search/index.php" align="left" method="get" class="search-form">
	<input type="text" size="35" name="p1" class="searchBox" value="" />
	<input type="submit" value="Start Searching!" />
</form>
**/ ?>