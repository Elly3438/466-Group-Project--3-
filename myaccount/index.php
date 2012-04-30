<?php
require('../includes/common.php');
require('../includes/helpers.inc.php');
require('../includes/begin.php');

if(isset($_SESSION['register'])){
	echo 'Registration Complete<br />';
	echo 'Thanks for registering, '. $_SESSION['firstname'] .'...<br />';
	echo 'Checking for cookie<br />';
	print_r($_COOKIE);
}

if(isset($_SESSION['username'])){
	$username = $_SESSION['username'];
	$usertype = get_user_type($_SESSION['username']);
	if($usertype == 1){
		header('Location: /myaccount/emp/');
		exit();
 	} 
	 elseif($usertype == 2){
		header('Location: /myaccount/cust/');
		exit();
 	}
	elseif($usertype == 3){
		header('Location: /myaccount/teach/');
		exit();
	}
}
else {
	header('Location: /login/index.php');
	exit();
} ?>
</div>
<?php
include('../includes/footer.php');
?>