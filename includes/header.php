<?php #temporary header info 
	  #Authors: Lila Papiernik and Jeffrey Bowden
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Kimball Music</title>
<link type="text/css" rel="stylesheet" media="all" href="<?php echo BASE_URL; ?>/css/style.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>/js/main.js"></script>
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
								echo '<a href="'.BASE_URL.'/myaccount/">My Account</a> | ';
								echo '<a href="'.BASE_URL.'/login/logout.php">Log Out</a>';
							}
							else {
								echo '<a href="'.BASE_URL.'/login/index.php">Log In</a></li>';
								echo '<li>|</li>';
								echo '<li><a href="'.BASE_URL.'/register">Sign Up</a>';
							}
						?>
					</li>
					<li>|</li>
					<li><a href="<?php echo BASE_URL; ?>/cart">My Cart 
						<?php
							if(isset($_SESSION['cart'])){
								echo '('.sizeof($_SESSION['cart']).')';
							}else{
								echo '(0)';
							}
						?>
					</a></li>
				</ul>
			</div>
		</div>
		<div id="header">
			<div id="title">
				<a href="<?php echo BASE_URL; ?>">Kimball Music</a>
			</div>
			
			<div id="search">
			<?php
			$searchdir = "";
			
			if (file_exists("../search/index.php"))
			{
				$searchdir = "../search/index.php";
			}
			else 
			{
					$searchdir = BASE_URL."/search/index.php";
			}

			?>
				<form action="<?php echo $searchdir; ?>" method="get">
				
					<fieldset id="searchbox">
						<input type="text" name="p1" value="" />
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
					<li><a href="<?php echo BASE_URL; ?>/search/index.php?p4=Violin">Violin</a></li>
					<li><a href="<?php echo BASE_URL; ?>/search/index.php?p4=Viola">Viola</a></li>
					<li><a href="<?php echo BASE_URL; ?>/search/index.php?p4=Cello">Cello</a></li>
					<li><a href="<?php echo BASE_URL; ?>/search/index.php?p4=Bass">Bass</a></li>
					<li><a href="<?php echo BASE_URL; ?>/search/index.php?p4=Accessories">Accessories</a></li>
				</ul>
			</div>
			
			<div id="social">
			</div>
		</div>
		
		<div id="main">