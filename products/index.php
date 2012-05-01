<?php
require('../includes/common.php');
require('../includes/helpers.inc.php');
include('../includes/searchhelper.php');
require('../includes/begin.php');

include('../includes/header.php');
?>
	
	
<div class="product-page-content">
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
	<div class = "product-name">
		<?php echo '<h2>'. ($drow['name'])  . '</h2>'; ?>
	</div>
	<div class="product-cost">
		<?php echo '<h2>$'. ($drow['unit_price'])  . '</h2>'; ?>
	</div>
	

	<div class="product-desc-view"> <p>
	
		<?php echo '<p>' . $drow['description'] . '<p>'; ?>
	</div>
	
	<div class="product-footer"></div>
	<div class="product-purchase">
		<form action="<?php echo BASE_URL; ?>/cart/" method="post">
			<input type="hidden" name="merch_id" value="<?php echo ($drow['merch_id']); ?>" />
			<input type="text" name="quantity" value="1" />
			<input type="submit" name="add" value="Purchase" />
		</form>
	</div>
</div>

<?php
	include('../includes/footer.php');
?>
