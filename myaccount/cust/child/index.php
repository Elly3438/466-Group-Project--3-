<?php
require('../../../includes/common.php');
require('../../../includes/helpers.inc.php');
require('../../../includes/begin.php');

include('../../../includes/header.php');
?>
<?php 
if(isset($_SESSION['username'])){
	$username = $_SESSION['username'];
	$usertype = get_user_type($_SESSION['username']);
?>

<div class="myaccount-title">Customer Page</div>
		<div id="tabs-wrapper">
			<ul class="tabs">
				<li class="tabs-click" ><a href="../">Account Overview</a></li>
				<li class="tabs-click" ><a href="../edit/">Edit Account Info</a></li>
				<li class="tabs-click" ><a href="../orders/">Order History</a></li>
				<li class="tabs-click active" ><a href="./">Child Info</a></li>
			</ul>
		</div>
		<div id="tab-content">
		</div>
<?php }
else {
	header('Location: /login/index.php');
	exit();
} ?>

<?php
include('../../../includes/footer.php');
?>