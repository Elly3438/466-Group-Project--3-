<?php /************************************
--Logout Script--
Author: Jeffrey Bowden
*****************************************/
require('../includes/common.php');
require('../includes/helpers.inc.php');
require('../includes/begin.php');

// If the user is logged in, delete the session vars to log them out
	$msg = '';
	session_start();
	if (isset($_SESSION['username'])) {
		
		$msg = remove_auth_token($_SESSION['username']);
			
		$_SESSION = array();
		
		if (isset($_COOKIE[session_name()])) {
		setcookie(session_name(), '', time() - 3600);
		}
		
		session_destroy();
	}
	setcookie(md5(COOKIE_NAME), '', time() - 3600, '/', 'groupproject.localhost');
	
	if($msg == ''){
		// Redirect to the home page
		header('Location: '.BASE_URL.'/index.php');
		exit();
	}
	else{
		echo $msg;
	}
?>