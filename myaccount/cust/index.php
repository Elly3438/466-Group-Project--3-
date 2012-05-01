<?php /************************************
--Customer Account Page--
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
				<li class="tabs-click" ><a href="./orders/">Order History</a></li>
				<li class="tabs-click" ><a href="./child">Child Info</a></li>
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
				<div class="acc-info"><h2>Child Information</h2>
				<?php $child_info = get_user_child_info($account_info->u_id, 'user'); 
				if($child_info->num_rows == 0) {
					echo 'You currently do not have any children registered.';
				}
				else { ?>
				 <ul>
					<?php while($row = mysqli_fetch_object($child_info)){ ?>
						<li>Name: <?php echo $row->firstname .' '. $row->lastname; ?> Age: <?php echo $row->age; ?></li>
						<?php $child_inst_info = get_current_child_inst_info($row->child_id); 
						if($child_inst_info->num_rows != 0){
							while($irow = mysqli_fetch_object($child_inst_info)){ ?>
							<li>Current Rental Instrument: <?php echo $irow->name; ?></li>
						<?php }
						}
						else{?>
							<li>No Current Instruments</li>
					<?php }
					}
				}
				?>
				   <li><div class="edit-link"><a href="./child/">Add/remove children &raquo;</a></div></li>
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