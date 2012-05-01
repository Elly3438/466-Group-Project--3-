<script type="text/javascript">
function actionsubmit()
{
  if(document.pressed == 'Update Cart')
  {
   document.cart.action ="";
  }
  else
  if(document.pressed == 'Checkout')
  {
    document.cart.action ="/checkout/";
  }
  return true;
}
</script>
<?php
/*
 * Author: Lila Papiernik
 */
	require('../includes/common.php');
	require('../includes/helpers.inc.php');
	require('../includes/begin.php');
	
	//Get the current cart
	if(isset($_SESSION['cart'])){
		//Update the cart
		if(isset($_POST['update'])){
			$tempcart = array();
			
			//Loop through each cart item
			$j=0;
			for($i=0; $i < $_POST['size']; $i++){
			
				//Don't add it to the new cart if they want to remove it
				if(!isset($_POST['delete'.$i])){
					//Add the product with the quantity specified
					if(!isset($_POST['rental'.$i]) || !isset($_POST['rent_id'.$i])){
						$tempcart[$j] = array( "id"=>$_SESSION['cart'][$i]['id'], "quantity"=>$_POST['quantity'.$i], "type"=>isset($_POST['rental'.$i]), "rent_id"=>NULL, "rent_bill"=>NULL, "rent_length"=>NULL );
					}else{
						$tempcart[$j] = array( "id"=>$_SESSION['cart'][$i]['id'], "quantity"=>$_POST['quantity'.$i], "type"=>isset($_POST['rental'.$i]), "rent_id"=>$_POST['rent_id'.$i], "rent_bill"=>$_POST['rent_bill'.$i], "rent_length"=>$_POST['rent_length'.$i] );
					}
					$j++;
				}
			}
			
			$_SESSION['cart'] = $tempcart;
		}
		
	}
	
	
	//Check if there's a new product to add to the cart 
	if(isset($_POST['merch_id'])){
	
		$new = true; //Start assuming the product we're adding is new to the cart
		
		//Loop through all current items in the cart to see if it's already there
		for($i=0; $i<sizeof($_SESSION['cart']); $i++) {
			if($_SESSION['cart'][$i]['id'] == $_POST['merch_id']){
				$_SESSION['cart'][$i]['quantity'] += $_POST['quantity'];
				$new = false; //It's not a new item
				break;
			}
		}
		
		//If the item is new
		if($new){
			$_SESSION['cart'][sizeof($_SESSION['cart'])] = array( "id"=>$_POST['merch_id'], "quantity"=>$_POST['quantity'], "type"=>isset($_POST['rental']), "rent_id"=>NULL, "rent_bill"=>NULL, "rent_length"=>NULL);
		}
	}
	
include('../includes/header.php');
?>
<form class="cart" name="cart" onsubmit="return actionsubmit();" method="post">
<?php
	if(isset($_SESSION['cart']) && sizeof($_SESSION['cart']) > 0 ){
?>
	<table>
		<tr>
			<td width="20px"><h1>Remove</h1></td>
			<td width="40px"><h1>Quantity</h1></td>
			<td><h1>Name</h1></td>
			<td><h1>Brand</h1></td>
			<td width="20px"><h1>Rental</h1></td>
			<td width="80px"><h1>Price</h1></td>
			<td width="80px"><h1>Total</h1></td>
		</tr>
		
		<input type="hidden" name="size" value="<?php echo sizeof($_SESSION['cart']); ?>"\>
<?php
		$total_price = 0;
		
		for($i=0; $i<sizeof($_SESSION['cart']); $i++){
			$product_info = get_product_info($_SESSION['cart'][$i]['id']);
			$total_price += $product_info->unit_price * $_SESSION['cart'][$i]['quantity'];
?>
		<tr>
			<td width="20px" style="text-align:center;"><input type="checkbox" name="delete<?php echo $i; ?>"></td>
			<td width="40px"><input type="text" name="quantity<?php echo $i; ?>" value="<?php echo $_SESSION['cart'][$i]['quantity']; ?>"></td>
			<td style="text-align:center;"><?php echo $product_info->name; ?></td>
			<td style="text-align:center;"><?php echo $product_info->brand; ?></td>
			<td width="40px" style="text-align:center;"><input type="checkbox" name="rental<?php echo $i; ?>" value="true" <?php if($_SESSION['cart'][$i]['type']){ echo'checked';} ?>></td>
			<td width="80px"><?php if(!$_SESSION['cart'][$i]['type']){echo '$'.$product_info->unit_price;}else{echo '--';} ?></td>
			<td width="80px"><?php if(!$_SESSION['cart'][$i]['type']){echo '$'.($product_info->unit_price * $_SESSION['cart'][$i]['quantity']);}else{echo '--';} ?></td>
		</tr>
<?php
			if($_SESSION['cart'][$i]['type']){
?>
		<tr>
			<td colspan="4">
				<select style="width:400px" name="rent_id<?php echo $i; ?>">
					<option selected value="NULL">Select One.</option>
					<?php
						$result = get_rentable_inst($_SESSION['cart'][$i]['id']);
						for($i=0;$i<$result->num_rows;$i++){
							$row = mysqli_fetch_object($result);
							echo '<option ';
							if($row->serial_num == $_SESSION['cart'][$i]['rent_id']){
								echo'selected ';
							}
							echo 'value="'.$row->serial_num.'">'.$row->serial_num." - ".$row->size." - ".$row->inst_condition." - Quarterly: $".$row->rentfee_quarterly." - Monthly: $".$row->rentfee_monthly.'</option>';
						}
					?>
				</select>
			</td colspan="2">
			<td>
				<select style="width:150px" name="rent_bill<?php echo $i; ?>">
					<option <?php if(!isset($_SESSION['cart'][$i]['rent_bill'])){ echo'selected';} ?> value="NULL">Select One.</option>
					<option <?php if(isset($_SESSION['cart'][$i]['rent_bill']) && $_SESSION['cart'][$i]['rent_bill'] == "Q"){ echo'selected';} ?> value="Q">Quarterly</option>
					<option <?php if(isset($_SESSION['cart'][$i]['rent_bill']) && $_SESSION['cart'][$i]['rent_bill'] == "M"){ echo'selected';} ?> value="M">Monthly</option>
				</select>
			</td>
			<td colspan="1">
				Length:
				<input type="text" name="rent_length<?php echo $i; ?>" value="<?php if(isset($_SESSION['cart'][$i]['rent_length'])){echo $_SESSION['cart'][$i]['rent_length'];} ?>" />
			</td>
		</tr>
<?php
			}
		}
		
		if(sizeof($_SESSION['cart']) > 0){
?>
		<tr>
			<td colspan="6" ><h1 style="text-align:right;">Total price:<h1></td>
			<td colspan="1">$<?php echo $total_price; ?></td>
		</tr>
		<input type="hidden" name="total" value="<?php echo $total_price; ?>" />
<?php
		}
?>
	</table>
<input type="submit" name="update" value="Update Cart" onclick="document.pressed=this.value" />
<input type="submit" name="checkout" value="Checkout" onclick="document.pressed=this.value" />
<?php
	}else{
?>
	<h1>Your cart is currently empty</h1>
<?php
	}
?>
</form>
<?php
	include('../includes/footer.php');
?>