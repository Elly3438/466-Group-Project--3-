<?php
/*
 * Author: Lila Papiernik
 */
	require('../includes/common.php');
	require('../includes/helpers.inc.php');
	require('../includes/begin.php');
	
	include('../includes/header.php');
	
	if(isset($_SESSION['firstname'])){
?>
	<div class="notice">
		<?php echo checkout_logged_in($_POST['total']); ?>
	</div>
<?php
	}else{
?>
	<div class="notice">
		Error, you need to be <a href="/login/">logged in</a>.
	</div>
<?php
	}
	include('../includes/footer.php');
?>