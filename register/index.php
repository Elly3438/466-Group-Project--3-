<?php /************************************
--Registration Page--
Author: Jeffrey Bowden
*****************************************/
require('../includes/common.php');
require('../includes/helpers.inc.php');
require('../includes/begin.php');

/***Processing the registration form submission ***/
$form_inputs = array(); //Store sql form values
$reg_errors = array(); //keep track of any errors in the form
$form_values = array('firstname' => 'First Name', 'lastname' => 'Last Name', 'street1' => 'Street 1', 'street2' => 'Street 2',
                     'city' => 'City', 'zip' => 'Zip Code', 'state' => 'State', 'phone' => 'Phone Number', 'email' => 'Email Address',
					 'username' => 'Username', 'password' => 'Password', 'password1' => 'Password');

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
	/** prepare phone number for processing **/
	$clean['phone'] = $clean['phone'];
	$clean['phone'] = preg_replace('/\D/', '', $clean['phone']);
	if(strlen($clean['phone']) == 11 && substr($clean['phone'], 0, 1) == 1){
		$clean['phone'] = substr($clean['phone'], 1);
	}
	
	/** process the form values and retrieve any errors **/
	foreach($clean as $key => $value){
		if(!empty($clean[$key])){
			$error = processInput($key, $value, $form_values);
			if($error != ''){
				$reg_errors[$key] = $error;
			}
		}
		else {
			if($key =='password1'){
				$reg_errors[$key] = 'Please verify your '. $form_values[$key] .'<br />';
			}
			elseif($key == 'street2'){
				#$reg_errors[$key] = '';
			}
			else{
				$reg_errors[$key] = 'You need to enter a '. $form_values[$key] .'<br />';
			}
		}
	}
	/** check if passwords match **/
	if(!isset($reg_errors['password1'])){
		if($clean['password'] != $clean['password1']){
			$reg_errors['password1'] = 'Passwords do not match.<br />';
		}
	}
	

	
	if(!empty($reg_errors)){
	/** display form errors **/
		foreach($reg_errors as $reg_error => $val){
				echo $val;
		}
	}
	else {
	/** add user to the db and log them in **/
		
		//prepare each value for secure sql entry
		foreach($clean as $key => $value){
			$form_inputs[$key] = mysqli_real_escape_string($dbc, $value);
		}

		$msg = addUser($form_inputs); //add the user
		
		if(preg_match('/^Error/', $msg)){
			echo $msg;
		}
		else {
			//log in and redirect
			if(setLoggedIn($form_inputs['username'], $form_inputs['password'], false)){
				$_SESSION['register'] = true;
				header('Location: ../myaccount/');
				exit();
			}
			else{
				header('Location: ../login/index.php');
				exit();
			}
		}
	}
}

include('../includes/header.php');
?>

<form action="/register/index.php" method="post" accept-charset="utf-8">
	<h1>REGISTRATION</h1>
	<div class="form-item" <?php echo (isset($reg_errors['firstname']) ? 'style="color: #ff0000;"' : ''); ?>>
		<label for="firstname">First Name
		<span class="small">Add your first name</span></label>
		<input type="text" maxlength="30" name="firstname" id="firstname" 
			value="<?php echo (isset($clean['firstname']) ? $clean['firstname'] : ''); ?>" > 
	</div>
	<div class="form-item" <?php echo (isset($reg_errors['lastname']) ? 'style="color: #ff0000;"' : ''); ?> >
		<label for="lastname">Last Name
		<span class="small">Add your last name</span></label>
		<input type="text" maxlength="30" name="lastname" id="lastname" 
			value="<?php echo (isset($clean['lastname']) ? $clean['lastname'] : ''); ?>" >
	</div>
	<div class="form-item" <?php echo (isset($reg_errors['street1']) ? 'style="color: #ff0000;"' : ''); ?>>
		<label for="street1" >Street 1
		<span class="small">Enter your street address</span></label>
		<input type="text" maxlength="100" name="street1" id="street1" 
			value="<?php echo (isset($clean['street1']) ? $clean['street1'] : ''); ?>" > 
	</div>
	<div class="form-item" <?php echo (isset($reg_errors['street2']) ? 'style="color: #ff0000;"' : ''); ?>>
		<label for="street2">Street 2
		<span class="small">(Optional)</span></label>
		<input type="text" maxlength="100" name="street2" id="street2" 
			value="<?php echo (isset($clean['street2']) ? $clean['street2'] : ''); ?>" > 
	</div>
	<div class="form-item" <?php echo (isset($reg_errors['city']) ? 'style="color: #ff0000;"' : ''); ?>>
		<label for="city">City
		<span class="small">Add your city</span></label>
		<input type="text" maxlength="30" name="city" id="city" 
			value="<?php echo (isset($clean['city']) ? $clean['city'] : ''); ?>" > 
	</div>
	<div class="form-item" <?php echo (isset($reg_errors['zip']) ? 'style="color: #ff0000;"' : ''); ?>>
		<label for="zip">Zip
		<span class="small">Add your mailing code</span></label>
		<input type="text" maxlength="10" name="zip" id="zip" 
			value="<?php echo (isset($clean['zip']) ? $clean['zip'] : ''); ?>" > 
	</div>
	<div class="form-item" <?php echo (isset($reg_errors['state']) ? 'style="color: #ff0000;"' : ''); ?>>
		<label for="state">State
		<span class="small">Add your state</span></label>
		<select name="state" size="1" id="state">
		<option value>--</option>
		<?php foreach($states_array as $state => $statecode){
			if(isset($clean['state'])){ $clean['state'] == $statecode ? $stateselect = 'selected' : $stateselect = ''; }
			echo '<option value="'. $statecode .'" '. $stateselect .'>'. $state .'</option>';
		}
		?>
		</select></div>
	<div class="form-item" <?php echo (isset($reg_errors['phone']) ? 'style="color: #ff0000;"' : ''); ?>>
		<label for="phone">Phone
		<span class="small">Add your home number</span></label>
		<input type="text" maxlength="20" name="phone" id="phone" 
			value="<?php echo (isset($clean['phone']) ? $clean['phone'] : ''); ?>" >
	</div>
	<div class="form-item" <?php echo (isset($reg_errors['email']) ? 'style="color: #ff0000;"' : ''); ?>>
		<label for="email">Email
		<span class="small">Add your valid email</span></label>
		<input type="text" maxlength="50" name="email" id="email" 
			value="<?php echo (isset($clean['email']) ? $clean['email'] : ''); ?>" >
	</div>
	<div class="form-item" <?php echo (isset($reg_errors['username']) ? 'style="color: #ff0000;"' : ''); ?>>
		<label for="username">Username
		<span class="small">This will be used for login</span></label>
		<input type="text" maxlength="20" name="username" id="username" 
			value="<?php echo (isset($clean['username']) ? $clean['username'] : ''); ?>" >
	</div>
	<div class="form-item" <?php echo (isset($reg_errors['password']) ? 'style="color: #ff0000;"' : ''); ?>>
		<label for="password">Password
		<span class="small">Minimum size 6 characters</span></label>
		<input type="password" maxlength="20" name="password" id="password" 
			value="<?php echo (isset($clean['password']) && empty($reg_errors['password']) ? $clean['password'] : ''); ?>" >
	</div>
	<div class="form-item" <?php echo (isset($reg_errors['password1']) ? 'style="color: #ff0000;"' : ''); ?>>
		<label for="password1">Verify Password
		<span class="small">Re-enter your passowrd</span></label>
		<input type="password" maxlength="20" name="password1" id="password1">
	</div>
	<div class="form-item">
		<label for="username">Account Type
		<span class="small">Will decide type of access <br><br><br><br></span></label>
		<input type="radio" name="user" value="cust" checked="checked"> Customer
	</div>
	<div class="form-item">
		<input type="radio" name="user" value="emp" <?php if(isset($clean['user'])) {
			echo ($clean['user']== 'emp' ? 'checked="checked"' : ''); } ?>> Employee
	</div>
	<div class="form-item">
		<input type="radio" name="user" value="teach" <?php if(isset($clean['user'])) {
			echo($clean['user'] == 'teach' ? 'checked="checked"' : ''); } ?>> Teacher
	</div>
	<div class="form-item">
		<button type="submit" name="new-registration" value="1">Register</button>
	</div>
	<h2>Already have an account? <a href="../register">Sign in!</a></h2>
</form>
<?php 
	include('../includes/footer.php');
?>