<?php /************************************
--Customer Orders Page--
Author: Jeffrey Bowden
*****************************************/
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
		<div id="tabs-wrapper">
			<ul class="tabs">
				<li class="tabs-click" ><a href="../">Account Overview</a></li>
				<li class="tabs-click" ><a href="../edit/">Edit Account Info</a></li>
				<li class="tabs-click active" ><a href="./">Order History</a></li>
				<li class="tabs-click" ><a href="../child">Child Info</a></li>
			</ul>
		</div>
		<div id="tab-content">
		<table class="cust-orders">
			<tr>
			  <th>Date
			  </th>
			  <th>Order #
			  </th>
			  <th>Status
			  </th>
			  <th>Purchases
			  </th>
			  <th>Rentals
			  </th>
			  <th>Total
			  </th>
			</tr>

			<?php $account_info = get_user_acc_info($username); 
			$cust_order_info = get_cust_order_info($account_info->u_id);
			if($cust_order_info->num_rows == 0){
				echo '<tr><td>No orders</td></tr>';
			}
			else{
				$i = 1;
				while($row = mysqli_fetch_object($cust_order_info)){ ?>
				<tr class="<?php echo $i%2 == 0 ? 'even' : 'odd'; ?>">
					<td><?php echo $row->order_date; ?></td>
					<td><?php echo $row->order_id; ?></td>
					<td><?php echo $row->status == 'C' ? 'Completed' : 'Incomplete'; ?></td>
					<td><?php echo get_sum_purchases($row->order_id); ?></td>
					<td><?php echo get_sum_rentals($row->order_id); ?></td>
					<td><?php echo $row->grand_total; ?></td>
				</tr>
			<?php $i++;
				}
			}
			?>
		</table>
		</div>
<?php }
else {
	header('Location: '.BASE_URL.'/login/index.php');
	exit();
} ?>

<?php
include('../../../includes/footer.php');
?>
