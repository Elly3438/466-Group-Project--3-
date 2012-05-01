<?php
/*
 * Author: Lila Papiernik
 */
	require('../includes/common.php');
	require('../includes/helpers.inc.php');
	require('../includes/begin.php');
	
	include('../includes/header.php');
	
?>
	<div class="notice">
<?php
	if(isset($_SESSION['firstname'])){
		if(isset($_SESSION['cart'])){
			echo checkout_logged_in($_POST['total']);
		}else{
			echo 'Error, your cart is empty.';
		}
		$_SESSION['cart'] = NULL;
	}else{
		echo 'Error, you need to be <a href="/login/">logged in</a>';
	}
?>
	</div>
<?php
	include('../includes/footer.php');
?>