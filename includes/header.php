<?php

	if(isset($_SESSION['firstname'])){
		echo 'Welcome, '. $_SESSION['firstname'] .'! | ';
  		echo '<a href="/login/logout.php">Log Out</a>';
  	}
  	else {
  		echo '<a href="/login/index.php">Log In</a>';
  	}
?>