<?php
require('../includes/common.php');
require('../includes/helpers.inc.php');
include('../includes/searchhelper.php');
require('../includes/begin.php');

if(isset($_SESSION['register'])){
	echo 'Registration Complete<br />';
	echo 'Thanks for registering, '. $_SESSION['firstname'] .'...<br />';
	echo 'Checking for cookie<br />';
	print_r($_COOKIE);
}

include('../includes/header.php');
?>
	
	
	<div class="product-page-content">
	<div class = "container-vi">
	</div>
	<div class = "photo-view">
		
		<?php
		
		
		global $dbc;
	/*	if(!isset($_GET['p7']))
		{
			die;
		}*/
		$item =  mysqli_query($dbc,"select * from merch_item where merch_id ='" . mysqli_real_escape_string($dbc,$_GET['p7']) . "';");
		$drow = mysqli_fetch_array($item);
	
		
					$picpath = "../photos/default.jpg";
						if(file_exists ( ("../photos/". $drow['merch_id'] . ".jpg")))
						{
							$picpath = ("../photos/". $drow['merch_id'] . ".jpg");
			 			}
						echo '<img src="'. $picpath .'" width="300" height="300" />';
	?>
	</div>
	<div class = "product-info-view">
		<?php
	
		
	echo '<h2>'. ($drow['name'])  . '</h2><br> Id'. ($drow['merch_id'])  . '<br> Qty 
	'. ($drow['has_inventory'])  . '<br>
	'. ($drow['unit_price'])  . '<br>' ;
?>
	</div>
	

	<div class="product-desc-view"> <p>';
	
	<?php echo '<p>' . $drow['description'] . '<p>'; ?>
</div>
</div>	
	<?php
include('../includes/footer.php');

?>