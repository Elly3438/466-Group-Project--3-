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

<div class="search-page-content">
<div style=" float:left;width:100%;">
<div style="float:left;">	
	<div class = "search-narrow-view">
	<!--insert javascript product view? -->
	<?php
	
	function get_catlist($idbc, $cat_item, $cat_var)
	{
	$catergories =  mysqli_query($idbc,"select distinct ". $cat_item ." from merch_item;");
	
	while ($row = mysqli_fetch_object($catergories))
	{
		
	foreach ($row as $value) {
		$lvalue = strtolower($value);
		
		 		
	    echo '<a href="index.php' . pass_var($value,$cat_var)   . '">' .  $value . '</a><br>';
	}

	unset($value);
		
	}
	}
	echo "<h3>Instruments</h3>";
	get_catlist($dbc, "category_name","item_limit_type" );
	echo "<h3>Brands</h3>";
	get_catlist($dbc, "brand","item_limit_brand");
	echo "<h3>Price Range</h3>";
	
	//get_catlist($dbc, "unit_price","item_limit_price");
	
	 echo '<a href="index.php?p3=50 ">' .  "< $50" . '</a><br>';
	 echo '<a href="index.php?p3=100 ">' .   "< $100" . '</a><br>';
	  echo '<a href="index.php?p3=250 ">' .  "< $250" . '</a><br>';
	 echo '<a href="index.php?p3=500 ">' .  "< $500" . '</a><br>';
	  
	?>

	</div>
	<div class ="search-prod-view">
		<?php
		$elcount = 0;
		
		global $dbc;
		
		$item_query = "select * from merch_item where ";
		
		$query_prior = false;
	
//master search query
		if(isset($_GET['p1']))
		{
			$p1_ver = mysqli_real_escape_string($dbc,$_GET['p1']);
			$item_query = $item_query . " ( category_name like '%". $p1_ver ."%' OR name like '%". $p1_ver ."%' or brand like  '%". $p1_ver ."%') ";
			
		}
		else 
		{
			$item_query = $item_query . " name like '%' " ;
		}
		
		
		if(isset($_GET['p2']))
		{
			$item_query = $item_query . " AND brand like '" . mysqli_real_escape_string($dbc,$_GET['p2']) ."' ";
			
		}
		
		if(isset($_GET['p3']))
		{
		$item_query = $item_query . " AND unit_price < " . mysqli_real_escape_string($dbc,$_GET['p3']) ." ";
			
						
			$query_prior = true;
		}
		if(isset($_GET['p4']))
		{
				
			$item_query = $item_query . " AND category_name like '" . mysqli_real_escape_string($dbc,$_GET['p4']) ."' ";
			
			$query_prior = true;
		}
		
		else {
			
		}
		//get number
		 $data = mysqli_query($dbc, $item_query) or die(mysql_error()); 
 $num_rows = mysqli_num_rows($data); 
	
		
		if(isset($_GET['p5']))
		{
			$intpage = ((($_GET['p5']) - 1) * 9);
			$item_query = $item_query . " LIMIT " . $intpage . ", 9";
		}
		else {
			$item_query = $item_query . " LIMIT 0, 9";
		}

		//echo  $item_query;
		
		$result = mysqli_query($dbc, $item_query);
		if(!$result){
			die;
		}
		
		 while($drow = mysqli_fetch_array($result))  {
		 				$picpath = "../photos/default.jpg";
						if(file_exists ( ("../photos/". $drow['merch_id'] . ".jpg")))
						{
							$picpath = ("../photos/". $drow['merch_id'] . ".jpg");
			 			}
						echo '<div class="element">
						<a href="../products/index.php?p7='. $drow['merch_id'] . '"><img src="'. $picpath .'" width="200" height="200" /></a>
		 				<div class="desc">
		 				'. ($drow['name'])  . '<br> Id:
		 				'. ($drow['merch_id'])  . '<br> Qty: 
		 				'. ($drow['has_inventory'])  . '<br>
		 				'. ($drow['unit_price'])  . '<br>
		 				
		 				</div>
						</div>
						';
			}
			unset($drow);
		
							 
		?>
	</div>
			<div style="width:100%; height:30px;   background-color:#eee">
				<?php
				$pgcount = 0;
				while ($pgcount*9 < $num_rows )
				{
					$pgcount++;
					echo ' <a href="'. pass_var( $pgcount,"item_page").'">'. $pgcount .'</a>';
				}
				
				?>
				
			</div>
</div>

</div>
<?php
include('../includes/footer.php');
?>