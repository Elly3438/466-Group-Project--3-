<?php /************************************
--Employee Account Page--
Author: Jeffrey Bowden
*****************************************/
require('../../includes/common.php');
require('../../includes/helpers.inc.php');
require('../../includes/begin.php');

?>
<?php 
if(isset($_SESSION['username'])){
	$username = $_SESSION['username'];
	$usertype = get_user_type($_SESSION['username']);
	
	include('../../includes/header.php');
?>

		<div id="tabs-wrapper">
			<ul class="tabs">
				<li class="tabs-click active" ><a href="./">Account Overview</a></li>
				<li class="tabs-click" ><a href="./edit/">Edit Account Info</a></li>
			</ul>
		</div>
		<div id="tab-content" style="width: 800px; margin: 0 auto">
			<?php $account_info = get_user_acc_info($username); ?>
				<div class="acc-info"><h2>Your Information</h2>
				 <ul>
				  <li>Username: <?php echo $username; ?></li>
				  <li>Email: <?php echo $account_info->email; ?></li>
				  <li>Name: <?php echo $account_info->firstname .' '. $account_info->lastname; ?></li>
				  <li>Phone Number: <?php echo $account_info->phone; ?></li>
				 </ul>
				 <h3>Address</h3>
				 <ul>
				  <li>Street 1: <?php echo $account_info->street1; ?></li>
				  <li>Street 2: <?php echo $account_info->street2; ?></li>
				  <li>City: <?php echo $account_info->city; ?></li>
				  <li>Zip Code: <?php echo $account_info->zip; ?></li>
				  <li>State: <?php echo $account_info->state; ?></li>
				  <li><div class="edit-link"><a href="./edit/">Edit Account Info &raquo;</a></div></li>
				 </ul></div>
		</div>
<?php }
else {
	header('Location: '.BASE_URL.'/login/index.php');
	exit();
} ?>

<?php
include('../../includes/footer.php');
?>