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
					$tempcart[$j] = array( "id"=>$_SESSION['cart'][$i]['id'], "quantity"=>$_POST['quantity'.$i], "type"=>1 );
					$j++;
				}
			}
			
			$_SESSION['cart'] = $tempcart;
		}
		
	} else { //Default cart set
		$_SESSION['cart'] = array(array( "id"=>1, "quantity"=>1, "type"=>1 ),array( "id"=>2, "quantity"=>1, "type"=>1 ));
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
		if($new)
			$_SESSION['cart'][sizeof($_SESSION['cart'])] = array( "id"=>$_POST['merch_id'], "quantity"=>$_POST['quantity'], "type"=>1);
	}
	
include('../includes/header.php');
?>
<form class="cart" name="cart" onsubmit="return actionsubmit();" method="post">
<?php
	if(isset($_SESSION['cart']) && sizeof($_SESSION['cart']) > 0 ){
?>
	<table>
		<tr>
			<td><h1>Delete</h1></td>
			<td><h1>Quantity</h1></td>
			<td><h1>Name</h1></td>
			<td><h1>Brand</h1></td>
			<td><h1>Price</h1></td>
			<td><h1>Total</h1></td>
		</tr>
		
		<input type="hidden" name="size" value="<?php echo sizeof($_SESSION['cart']); ?>"\>
<?php
		$total_price = 0;
		
		for($i=0; $i<sizeof($_SESSION['cart']); $i++){
			$product_info = get_product_info($_SESSION['cart'][$i]['id']);
			$total_price += $product_info->unit_price * $_SESSION['cart'][$i]['quantity'];
?>
		<tr>
			<td><input type="checkbox" name="delete<?php echo $i; ?>"></td>
			<td><input type="text" name="quantity<?php echo $i; ?>" value="<?php echo $_SESSION['cart'][$i]['quantity']; ?>"></td>
			<td><?php echo $product_info->name; ?></td>
			<td><?php echo $product_info->brand; ?></td>
			<td>$<?php echo $product_info->unit_price; ?></td>
			<td>$<?php echo $product_info->unit_price * $_SESSION['cart'][$i]['quantity']; ?></td>
		</tr>
<?php
		}
		
		if(sizeof($_SESSION['cart']) > 0){
?>
		<tr>
			<td colspan="2"><h1>Total price<h1></td>
			<td colspan="4">$<?php echo $total_price; ?></td>
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