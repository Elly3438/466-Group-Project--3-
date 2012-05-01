<?php
	require('../includes/common.php');
	require('../includes/helpers.inc.php');
	require('../includes/begin.php');
	
	include('../includes/header.php');
	
	if(isset($_SESSION['firstname'])){
		checkout_logged_in($_POST['total']);
?>
	<div class="notice">
		Congratulations! Your purchase has been placed.
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