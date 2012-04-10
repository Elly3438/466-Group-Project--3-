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

include('../includes/header.php');
?>
<div id="page-content">
<?php 
if(isset($_SESSION['username'])){
	if(get_user_type($_SESSION['username']) == 1){?>
	<div class="myaccount-title">Employee Page</div>
		<div id="tabs-wrapper">
			<ul class="tabs">
				<li><a href="javascript:void(0);" id="tabs-click" name="empaccount">Account Overview</a></li>
				<li><a href="javascript:void(0);" id="tabs-click" name="editaccount">Edit Account Info</a></li>
				<li><a href="javascript:void(0);" id="tabs-click" name="orderhistory">Order History</a></li>
				<li><a href="javascript:void(0);" id="tabs-click" name="childinfo">Child Info</a></li>
			</ul>
		</div>

<?php } 
	 elseif(get_user_type($_SESSION['username']) == 2){?>
	<div class="myaccount-title">Customer Page</div>
		<div id="tabs-wrapper">
			<ul class="tabs">
				<li><a href="javascript:void(0);" id="tabs-click" name="custaccount">Account Overview</a></li>
				<li><a href="javascript:void(0);" id="tabs-click" name="editaccount">Edit Account Info</a></li>
				<li><a href="javascript:void(0);" id="tabs-click" name="orderhistory">Order History</a></li>
				<li><a href="javascript:void(0);" id="tabs-click" name="childinfo">Child Info</a></li>
			</ul>
		</div>


<?php }
	elseif(get_user_type($_SESSION['username']) == 3){?>
	<div class="myaccount-title">Teacher Page</div>
		<div id="tabs-wrapper">
			<ul class="tabs">
				<li><a href="javascript:void(0);" id="tabs-click" name="teachaccount">Account Overview</a></li>
				<li><a href="javascript:void(0);" id="tabs-click" name="editaccount">Edit Account Info</a></li>
				<li><a href="javascript:void(0);" id="tabs-click" name="orderhistory">Order History</a></li>
				<li><a href="javascript:void(0);" id="tabs-click" name="childinfo">Child Info</a></li>
			</ul>
		</div>

<?php }
}
else {
	header('Location: /login/index.php');
	exit();
} ?>
</div>
<?php
include('../includes/footer.php');
?>