<?php /************************************
--My Account Main Page--
Author: Jeffrey Bowden
*****************************************/
require('../includes/common.php');
require('../includes/helpers.inc.php');
require('../includes/begin.php');

if(isset($_SESSION['username'])){
	$username = $_SESSION['username'];
	$usertype = get_user_type($_SESSION['username']);
	if($usertype == 1){
		header('Location: '.BASE_URL.'/myaccount/emp/');
		exit();
 	}
	 elseif($usertype == 2){
		header('Location: '.BASE_URL.'/myaccount/cust/');
		exit();
 	}
	elseif($usertype == 3){
		header('Location: '.BASE_URL.'/myaccount/teach/');
		exit();
	}
}
else {
	header('Location: '.BASE_URL.'/login/index.php');
	exit();
}
include('../includes/header.php'); ?>

<?php
include('../includes/footer.php');
?>