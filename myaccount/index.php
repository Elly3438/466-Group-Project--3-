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
	$username = $_SESSION['username'];
	$usertype = get_user_type($_SESSION['username']);
	if($usertype == 1){?>
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
	 elseif($usertype == 2){?>
	<div class="myaccount-title">Customer Page</div>
		<div id="tabs-wrapper">
			<ul class="tabs">
				<li class="tabs-click active" name="tab1" >Account Overview</li>
				<li class="tabs-click" name="tab2" >Edit Account Info</li>
				<li class="tabs-click" name="tab3" >Order History</li>
				<li class="tabs-click" name="tab4" >Child Info</li>
			</ul>
		</div>
		<div id="tabs-content">
			<div id="tab1" >
			<?php $account_info = get_user_acc_info($username); ?>
				<div class="acc-info" style="float: left"><h2>Your Information</h2>
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
				  <li>Zip Code: <?php echo $account_info->zip; ?></li>
				  <li>State: <?php echo $account_info->state; ?></li>
				 </ul>
				<div class="edit-info" name="tab2" style="font-size: 12px; cursor: pointer">Edit Account Info &raquo;</div>
				</div>
				<div class="child-info" style="float: left; margin-left: 20px"><h2>Child Information</h2>
				<?php $child_info = get_user_child_info($account_info->u_id); 
				if($child_info->num_rows == 0) {
					echo 'You currently do not have any children registered.';
				}
				else { ?>
				 <ul>
					<?php while($row = mysqli_fetch_object($child_info)){
						echo '<li>Name: '. $row->firstname .' '. $row->lastname .'</li>';
						echo '<li>Age: '. $row->age .'</li>';
					}
				}
				?>
				 </ul>
				<div class="edit-info" name="tab4" style="font-size: 12px; cursor: pointer">Add/remove children &raquo;</div>
				</div>
				<div class="current-rentals" style="float: left; margin-left: 20px"><h2>Current Rentals</h2>
				</div>
			</div>
			<div id="tab2" style="display:none">TAB2
			</div>
			<div id="tab3" style="display:none">TAB3
			</div>
			<div id="tab4" style="display:none">TAB4
			</div>
		</div>


<?php }
	elseif($usertype == 3){?>
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