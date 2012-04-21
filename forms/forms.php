<?php /************************************
--Registration Page--
Author: Jeffrey Bowden
*****************************************/
require('../includes/common.php');
require('../includes/helpers.inc.php');
?>
<?php 
/***Processing the registration form submission ***/
$form_inputs = array(); //Store form values
$reg_errors = array(); //keep track of any errors in the form

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	
	/** Initial POST cleaning **/
	$clean = array();
	foreach($_POST as $key => $value){
		$value = strip_tags(trim($value)); //strip out script tags and extra space
		$value = preg_replace('/"/', '', $value); //replace any " that may break the code
		$clean[$key] = $value;
	}
	/** type processing **/
	if(isset($clean['user'])){
		switch ($clean['user']){
			case 'rent':
				$form_inputs['type'] = 1;
				break;
			case 'order':
				$form_inputs['type'] = 2;
				break;
		}
	}
	else {
		echo 'I picked no user type<br />';
	}
	/** first name processing **/
	if(!empty($clean['firstname'])){
		if(preg_match('/^[A-Z \'.-]+$/i', $clean['firstname'])){
			$form_inputs['firstname'] = mysql_real_escape_string($clean['firstname']);		
		} 
		else {
			$reg_errors['fname'] = 'First Name needs to be valid characters<br />';
		}
	}
	else {
		$reg_errors['fname'] = 'You need to enter a First Name.<br />';
	}
	/** last name processing **/
	if(!empty($_POST['lastname'])){
		if(preg_match('/^[A-Z \'.-]+$/i', trim($_POST['lastname']))){
			$form_inputs['lastname'] = mysql_real_escape_string(trim($_POST['lastname']));		
		}
		else {
			$reg_errors['lname'] = 'Last Name needs to be valid characters<br />';
		}
	}
	else {
		$reg_errors['lname'] = 'You need to enter a Last Name.<br />';
	}
	/** street1 processing **/
	if(!empty($_POST['street1'])){
		if(preg_match('/^[A-Z0-9 \'.-]+$/i', trim($_POST['street1']))){
			$form_inputs['street1'] = mysql_real_escape_string(trim($_POST['street1']));		
		}
		else {
			$reg_errors['street1'] = 'Street1 needs to be valid characters<br />';
		}
	}
	else {
		$reg_errors['street1'] = 'You need to enter Street1.<br />';
	}
	/** street2 processing **/
	if(!empty($_POST['street2'])){
		if(preg_match('/^[A-Z0-9 \'.-]+$/i', trim($_POST['street2']))){
			$form_inputs['street2'] = mysql_real_escape_string(trim($_POST['street2']));		
		}
		else {
			$reg_errors['street2'] = 'Street2 needs to be valid characters<br />';
		}
	}
	else {
		$form_inputs['street2'] = '';
	}
	/** city processing **/
	if(!empty($_POST['city'])){
		if(preg_match('/^[A-Z \'.-]+$/i', trim($_POST['city']))){
			$form_inputs['city'] = mysql_real_escape_string(trim($_POST['city']));		
		}
		else {
			$reg_errors['city'] = 'City needs to be valid characters<br />';
		}
	}
	else {
		$reg_errors['city'] = 'You need to enter City.<br />';
	}
	/** zip code processing **/
	if(!empty($_POST['zip'])){
		if(preg_match('/^([0-9]{5})(-[0-9]{4})?$/i', trim($_POST['zip']))){
			$form_inputs['zip'] = mysql_real_escape_string(trim($_POST['zip']));		
		}
		else {
			$reg_errors['zip'] = 'Zip code needs to be 5 digits, or in the format 12345-1234<br />';
		}
	}
	else {
		$reg_errors['zip'] = 'You need to enter Zip.<br />';
	}
	/**state processing **/
	if($_POST['state'] != ''){
		$form_inputs['state'] = mysql_real_escape_string($_POST['state']);		
	}
	else {
		$reg_errors['state'] = 'You need to select a State.<br />';
	}
	/**phone number processing - US phone numbers only **/
	if(!empty($_POST['phone'])){
		$phone = $_POST['phone'];
		$phone = preg_replace('/\D/', '', $phone);
		if(strlen($phone) == 11 && substr($phone, 0, 1) == 1){
			$phone = substr($phone, 1);
		}
		if(preg_match('/^([0-9]{10})$/i', $phone)){
			$form_inputs['phone'] = mysql_real_escape_string($phone);	
		}
		elseif(preg_match('/^([0-9]{7})$/i', $phone)){
			$reg_errors['phone'] = 'You need to enter an area code for your Phone Number<br />';
		}
		else {
			$reg_errors['phone'] = 'Phone Number needs to be valid characters and format 888-888-8888<br />';
		}
	}
	else {
		$phone = '';
		$reg_errors['phone'] = 'You need to enter a Phone Number.<br />';
	}
	/** Email processing **/
	if(!empty($_POST['email'])){
		if (filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL)) {
			if(form_validate_unique(trim($_POST['email']), 'email')){
				$form_inputs['email'] = mysql_real_escape_string(trim($_POST['email']));
			}
			else {
				$reg_errors['email'] = 'Email already in use.<br />';
			}
		}
		else {
			$reg_errors['email'] = 'Please enter a valid email address.<br />';
		}
	} 
	else {
		$reg_errors['email'] = 'You need to enter an Email.<br />';
	}
	/** Username processing **/
	if(!empty($_POST['username'])){
		if (preg_match('/^[A-Z0-9]{4,20}$/i', trim($_POST['username']))) {
			if(form_validate_unique(trim($_POST['username']), 'username')){
				$form_inputs['username'] = mysql_real_escape_string(trim($_POST['username']));
			}
			else {
				$reg_errors['username'] = 'Username already in use.<br />';
			}
		}
		else {
			$reg_errors['username'] = 'Please enter a valid Username (4-20 characters long).<br />';
		}
	} 
	else {
		$reg_errors['username'] = 'You need to enter a Username.<br />';
	}
	/** Date processing **/
	if(!empty($_POST['rent_start']))
	{
	
	
	}
	
	/** Password processing **/
	if(!empty($_POST['password'])){
		if (preg_match("/^(\w*(?=\w*\d)(?=\w*[a-z])(?=\w*[A-Z])\w*){6,20}$/", trim($_POST['password']))) {
			if(!empty($_POST['password1'])){
				if(trim($_POST['password']) == trim($_POST['password1'])){
					$form_inputs['password'] = mysql_real_escape_string(trim($_POST['password']));
				}
				else {
					$reg_errors['password1'] = 'Passwords do not match.<br />';
				}
			}
			else {
				$reg_errors['password1'] = 'You need to verify your Password.<br />';
			}
		}
		else {
			$reg_errors['password'] = 'Your Password is not valid (Must be 6-20 characters long, contain at least one caps character and one number).<br />';
		}
	} 
	else {
		$reg_errors['password'] = 'You need to enter a Password.<br />';
	}
	
	if(!empty($reg_errors)){
	/** display form errors **/
		foreach($reg_errors as $reg_error => $val){
				echo $val;
		}
	}
	else {
	/** add user to the db and log them in **/
		$msg = addUser($form_inputs);
		if(preg_match('/^Error/', $msg)){
			echo $msg;
		}
		else {
			if(setLoggedIn($form_inputs['username'], $form_inputs['password'], false)){
				header('Location: ../myaccount/');
			}
			else{
				header('Location: ../index.php');
			}
		}
	}
}

include('../includes/header.php');
?>

<form action="/register/index.php" method="post" accept-charset="utf-8">
<div class="sectionL" style="float:left;height:400">
	<div>
	Billing infomation
	</div>
	<div class="form-item">
		<input type="radio" name="user" value="rent" checked="checked">Rent
	</div>
	<div class="form-item">
		<input type="radio" name="user" value="order" <?php if(isset($_POST['user'])){if($_POST['user'] == 'emp') echo 'checked="checked"';} ?>>Order
	</div>
	<div class="form-item" <?php if(isset($reg_errors['fname'])) { echo 'style="color: #ff0000;"'; } ?>>First Name
		<input type="text" maxlength="30" name="firstname" 
			<?php if(isset($clean['firstname'])){ 
					echo 'value="'. $clean['firstname'] .'"'; 
			} ?>>
	</div>
	<div class="form-item" <?php if(isset($reg_errors['lname'])) { echo 'style="color: #ff0000;"'; } ?>>Last Name
		<input type="text" maxlength="30" name="lastname"
			<?php if(isset($_POST['lastname'])){ 
					echo 'value="'. $_POST['lastname'] .'"'; 
			} ?>>
	</div>
	<div class="form-item" <?php if(isset($reg_errors['street1'])) { echo 'style="color: #ff0000;"'; } ?>>Street 1
		<input type="text" maxlength="100" name="street1"
			<?php if(isset($_POST['street1'])){ 
					echo 'value="'. $_POST['street1'] .'"'; 
			} ?>>
	</div>
	<div class="form-item" <?php if(isset($reg_errors['street2'])) { echo 'style="color: #ff0000;"'; } ?>>Street 2
		<input type="text" maxlength="100" name="street2"
			<?php if(isset($_POST['street2'])){ 
					echo 'value="'. $_POST['street2'] .'"'; 
			} ?>>
	</div>
	<div class="form-item" <?php if(isset($reg_errors['city'])) { echo 'style="color: #ff0000;"'; } ?>>City
		<input type="text" maxlength="30" name="city" 
			<?php if(isset($_POST['city'])){ 
					echo 'value="'. $_POST['city'] .'"'; 
			} ?>>
	</div>
	<div class="form-item" <?php if(isset($reg_errors['zip'])) { echo 'style="color: #ff0000;"'; } ?>>Zip
		<input type="text" maxlength="10" name="zip"
			<?php if(isset($_POST['zip'])){ 
					echo 'value="'. $_POST['zip'] .'"'; 
			} ?>>
	</div>
	<div class="form-item" <?php if(isset($reg_errors['state'])) { echo 'style="color: #ff0000;"'; } ?>>State
		<select name="state" size="1">
		<option value>--</option>
		<?php foreach($states_array as $state => $statecode){
			if(isset($_POST['state'])){ if($_POST['state'] == $statecode) $stateselect = 'selected'; else $stateselect ='';}
			echo '<option value="'. $statecode .'"'. $stateselect .'>'. $state .'</option>';
		}
		?>
		</select></div>
	<div class="form-item" <?php if(isset($reg_errors['phone'])) { echo 'style="color: #ff0000;"'; } ?>>Phone
		<input type="text" maxlength="20" name="phone"
			<?php if(isset($_POST['phone'])){ 
					echo 'value="'. $phone .'"'; 
			} ?>>
	</div>
	<div class="form-item" <?php if(isset($reg_errors['phone'])) { echo 'style="color: #ff0000;"'; } ?>>Phone
	<select name="month">
	<option value="1">January
	<option value="2">February
	<option value="3">March
	<option value="4">April
	<option value="5">May
	<option value="6">June
	<option value="7">July
	<option value="8">August
	<option value="9">September
	<option value="10">October
	<option value="11">November
	<option value="12">December
	</select>
	<select name="day">
	<option value="1">1
	<option value="2">2
	<option value="3">3
	<option value="4">4
	<option value="5">5
	<option value="6">6
	<option value="7">7
	<option value="8">8
	<option value="9">9
	<option value="10">10
	<option value="11">11
	<option value="12">12
	<option value="13">13
	<option value="14">14
	<option value="15">15
	<option value="16">16
	<option value="17">17
	<option value="18">18
	<option value="19">19
	<option value="20">20
	<option value="21">21
	<option value="22">22
	<option value="23">23
	<option value="24">24
	<option value="25">25
	<option value="26">26
	<option value="27">27
	<option value="28">28
	<option value="29">29
	<option value="30">30
	<option value="31">31
	</select>
	<select name="year">
	<?php
	$today = getdate();
	$year = $today['year'];
	echo '<option value="' . strval($year) . '">' . strval($year);
    echo '<option value="' . strval($year+1) . '">' . strval($year+1);
	?>
		</select>
	</div>
	<div class="form-item">
		<button type="Finalize" name="new-registration" value="1">Register</button>
	</div>
</div>
<div class="sectionR" style="float:right;height:400">
<?php 
echo"<div> Order total: " .  $_Ordertotal . "</div>";    
echo"<div> Tax: " .  $_Tax . "</div>";    
echo"<div> Grand Total: " .  $_Grandtotal . "</div>";    


?>
</div>
</form>