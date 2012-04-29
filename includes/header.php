<?php #temporary header info ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Kimball Music</title>
<link type="text/css" rel="stylesheet" media="all" href="../css/style.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script type="text/javascript" src="/js/main.js"></script>
</head>
<body>

<!-- added parts -->
<div style = "width:900px;margin:0px, auto; " >

<?php
	if(isset($_SESSION['firstname'])){
		echo 'Welcome, '. $_SESSION['firstname'] .'! | ';
  		echo '<a href="/login/logout.php">Log Out</a>';
  	}
  	else {
  		echo '<a href="/login/index.php">Log In</a>';
  	}
	
?>

<?php
$searchdir = "";

if (file_exists("../search/index.php"))
{
	$searchdir = "../search/index.php";
}
else 
{
		$searchdir = "search/index.php";
}
echo '<form action="' . $searchdir .'" align="left" method="get" class="search-form">
	<input type="text" size="35" name="p1" class="searchBox" value="" />
	<input type="submit" value="Start Searching!" />
</form>'

?>
<!-- added parts -->
</div>
