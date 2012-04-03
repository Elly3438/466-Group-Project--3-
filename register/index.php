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
	$post_array = $_POST;
	echo $post_array['firstname'];
	foreach($post_array as &$value){
		echo $value;
		$value = strip_tags(trim($value)); //strip out script tags and extra space
		$value = preg_replace('/"/', '', $value); //replace any " that may break the code
	}
	print_r($post_array);
	
	/** type processing **/
	if(isset($post_array['user'])){
		switch ($post_array['user']){
			case 'emp':
				$form_inputs['type'] = 1;
				break;
			case 'cust':
				$form_inputs['type'] = 2;
				break;
			case 'teach':
				$form_inputs['type'] = 3;
				break;
		}
	}
	else {
		echo 'I picked no user type<br />';
	}
	/** first name processing **/
	if(!empty($post_array['firstname'])){
		if(preg_match('/^[A-Z \'.-]+$/i', $post_array['firstname'])){
			$form_inputs['firstname'] = mysql_real_escape_string($post_array['firstname']);		
		} 
		else {
			echo $post_array['firstname'];
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
			$reg_errors['password'] = 'Your Password is not valid (Must be 8-20 characters long, contain at least one caps character and one number).<br />';
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
	<div class="form-item">
		<input type="radio" name="user" value="cust" checked="checked">Customer
	</div>
	<div class="form-item">
		<input type="radio" name="user" value="emp" <?php if(isset($_POST['user'])){if($_POST['user'] == 'emp') echo 'checked="checked"';} ?>>Employee
	</div>
	<div class="form-item">
		<input type="radio" name="user" value="teach" <?php if(isset($_POST['user'])){if($_POST['user'] == 'teach') echo 'checked="checked"';} ?>>Teacher
	</div>
	<div class="form-item" <?php if(isset($reg_errors['fname'])) { echo 'style="color: #ff0000;"'; } ?>>First Name
		<input type="text" maxlength="30" name="firstname" 
			<?php if(isset($post_array['firstname'])){ 
					echo 'value="'. $post_array['firstname'] .'"'; 
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
	<div class="form-item" <?php if(isset($reg_errors['email'])) { echo 'style="color: #ff0000;"'; } ?>>Email
		<input type="text" maxlength="50" name="email" 
			<?php if(isset($_POST['email'])){ 
					echo 'value="'. $_POST['email'] .'"'; 
			} ?>>
	</div>
	<div class="form-item" <?php if(isset($reg_errors['username'])) { echo 'style="color: #ff0000;"'; } ?>>Username
		<input type="text" maxlength="20" name="username"
			<?php if(isset($_POST['username'])){ 
					echo 'value="'. $_POST['username'] .'"'; 
			} ?>>
	</div>
	<div class="form-item" <?php if(isset($reg_errors['password'])) { echo 'style="color: #ff0000;"'; } ?>>Password
		<input type="password" maxlength="20" name="password" 
			<?php if(isset($_POST['password']) && !isset($reg_errors['password'])){ 
					echo 'value="'. $_POST['password'] .'"'; 
			} ?>>
	</div>
	<div class="form-item" <?php if(isset($reg_errors['password1'])) { echo 'style="color: #ff0000;"'; } ?>>Verify Password
		<input type="password" maxlength="20" name="password1">
	</div>
	<div class="form-item">
		<button type="submit" name="new-registration" value="1">Register</button>
	</div>
</form>