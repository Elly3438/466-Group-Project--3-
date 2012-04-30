<?php
require('../../../includes/common.php');
require('../../../includes/helpers.inc.php');
require('../../../includes/begin.php');

/***Processing the edit form submissions ***/
$form_inputs = array(); //Store sql form values
$reg_errors = array(); //keep track of any errors in the form
$form_values = array('firstname' => 'First Name', 'lastname' => 'Last Name', 'street1' => 'Street 1', 'street2' => 'Street 2',
		'city' => 'City', 'zip' => 'Zip Code', 'state' => 'State', 'phone' => 'Phone Number', 'email' => 'Email Address',
		'username' => 'Username', 'password' => 'Password', 'password1' => 'Password');

$username = $_SESSION['username'];
$account_info = get_user_acc_info($username);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	/** Initial POST cleaning **/
	$clean = array();
	foreach($_POST as $key => $value){
		$value = strip_tags(trim($value)); //strip out script tags and extra space
		$value = preg_replace('/"/', '', $value); //replace any " that may break the code
		$clean[$key] = $value;
	}
	
	if(isset($_POST['info_change'])) {
		echo 'Info submitted';
		
		/** prepare phone number for processing **/
		$clean['phone'] = $clean['phone'];
		$clean['phone'] = preg_replace('/\D/', '', $clean['phone']);
		if(strlen($clean['phone']) == 11 && substr($clean['phone'], 0, 1) == 1){
			$clean['phone'] = substr($clean['phone'], 1);
		}
		unset($clean['info_change']);
		
		foreach($clean as $key => $value){
			if($value == $account_info->$key){
				unset($clean[$key]);
			}
		}
		
		/** process the form values and retrieve any errors **/
		if(!empty($clean)){
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
		}
	}
	elseif(isset($_POST['address_change'])) {
		echo 'Address submitted';
		
		unset($clean['address_change']);
		
		foreach($clean as $key => $value){
			if($value == $account_info->$key){
				unset($clean[$key]);
			}
		}

		/** process the form values and retrieve any errors **/
		if(!empty($clean)){
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
		}
	}
	elseif(isset($_POST['password_change'])) {
		echo 'Password submitted';
		
		unset($clean['password_change']);
		
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
			else{
				unset($clean['password1']);
			}
		}
	}
	
	if(empty($reg_errors) && !empty($clean)){
		/** update user info **/
	
		//prepare each value for secure sql entry
		foreach($clean as $key => $value){
			$form_inputs[$key] = mysqli_real_escape_string($dbc, $value);
		}
		if(isset($form_inputs['password'])){
			$form_inputs['password'] = crypt(md5($form_inputs['password']),md5($username));
		}
	
		$msg = updateUser($username, $form_inputs); //update the user
	
		if(preg_match('/^Error/', $msg)){
			echo $msg;
		}
		else {
			echo $msg;
		}
	}
}

include('../../../includes/header.php');

if(isset($_SESSION['username'])){
	$username = $_SESSION['username'];
	$usertype = get_user_type($_SESSION['username']);
?>

		<div id="tabs-wrapper">
			<ul class="tabs">
				<li class="tabs-click" ><a href="../">Account Overview</a></li>
				<li class="tabs-click active" ><a href="./">Edit Account Info</a></li>
				<li class="tabs-click" ><a href="../orders/">Order History</a></li>
				<li class="tabs-click" ><a href="../child">Child Info</a></li>
			</ul>
		</div>
		<?php 
          if(!empty($reg_errors)){
		    /** display form errors **/
		    foreach($reg_errors as $reg_error => $val){
			echo $val;
		    }
	      }
	    ?>
		<div id="tab-content" style="width: 800px; margin: 0 auto">
		  <div class="edit-box">
		    <form action="./index.php" method="post" accept-charset="utf-8">
		    <h2>Edit Your Information</h2>
		      <div class="form-item" <?php echo (isset($reg_errors['firstname']) ? 'style="color: #ff0000;"' : ''); ?>>
		        <label for="firstname">First Name</label>
		        <input type="text" maxlength="30" name="firstname" id="firstname" 
			value="<?php echo (isset($clean['firstname']) ? $clean['firstname'] : $account_info->firstname); ?>" > 
	          </div>
	          <div class="form-item" <?php echo (isset($reg_errors['lastname']) ? 'style="color: #ff0000;"' : ''); ?>>
		        <label for="firstname">Last Name</label>
		        <input type="text" maxlength="30" name="lastname" id="lastname" 
			     value="<?php echo (isset($clean['lastname']) ? $clean['lastname'] : $account_info->lastname); ?>" > 
	          </div>
	          <div class="form-item" <?php echo (isset($reg_errors['email']) ? 'style="color: #ff0000;"' : ''); ?>>
		        <label for="email">Email</label>
		        <input type="text" maxlength="50" name="email" id="email" 
			     value="<?php echo (isset($clean['email']) ? $clean['email'] : $account_info->email); ?>" >
	          </div>
	          <div class="form-item" <?php echo (isset($reg_errors['phone']) ? 'style="color: #ff0000;"' : ''); ?>>
		        <label for="phone">Phone</label>
		        <input type="text" maxlength="20" name="phone" id="phone" 
			     value="<?php echo (isset($clean['phone']) ? $clean['phone'] : $account_info->phone); ?>" >
	          </div>
	          <div class="form-item">
		        <button type="submit" name="info_change" value="1">Submit</button>
	          </div>
	        </form>
	        </div>
	        <div class="edit-box">
	        <form action="./index.php" method="post" accept-charset="utf-8">
	        <h2>Address Information</h2>
	          <div class="form-item" <?php echo (isset($reg_errors['street1']) ? 'style="color: #ff0000;"' : ''); ?>>
		        <label for="street1" >Street 1</label>
		        <input type="text" maxlength="100" name="street1" id="street1" 
			      value="<?php echo (isset($clean['street1']) ? $clean['street1'] : $account_info->street1); ?>" > 
	          </div>
	          <div class="form-item" <?php echo (isset($reg_errors['street2']) ? 'style="color: #ff0000;"' : ''); ?>>
		        <label for="street2">Street 2</label>
		        <input type="text" maxlength="100" name="street2" id="street2" 
			     value="<?php echo (isset($clean['street2']) ? $clean['street2'] : $account_info->street2); ?>" > 
	          </div>
	          <div class="form-item" <?php echo (isset($reg_errors['city']) ? 'style="color: #ff0000;"' : ''); ?>>
		        <label for="city">City</label>
		        <input type="text" maxlength="30" name="city" id="city" 
			     value="<?php echo (isset($clean['city']) ? $clean['city'] : $account_info->city); ?>" > 
	          </div>
	          <div class="form-item" <?php echo (isset($reg_errors['zip']) ? 'style="color: #ff0000;"' : ''); ?>>
		        <label for="zip">Zip</label>
		        <input type="text" maxlength="10" name="zip" id="zip" 
			     value="<?php echo (isset($clean['zip']) ? $clean['zip'] : $account_info->zip); ?>" > 
	          </div>
	          <div class="form-item" <?php echo (isset($reg_errors['state']) ? 'style="color: #ff0000;"' : ''); ?>>
		        <label for="state">State</label>
		        <select name="state" size="1" id="state">
		        <option value>--</option>
		      <?php foreach($states_array as $state => $statecode){
			    if(isset($clean['state'])){ $clean['state'] == $statecode ? $stateselect = 'selected' : $stateselect = '';
			    }
			    else {
			    	$account_info->state == $statecode ? $stateselect = 'selected' : $stateselect = '';
			    }
			    echo '<option value="'. $statecode .'" '. $stateselect .'>'. $state .'</option>';
		      }
		      ?>
		        </select></div>
		      <div class="form-item">
		        <button type="submit" name="address_change" value="1">Submit</button>
	          </div>
		    </form>
		    </div>
		    <div class="edit-box">
		    <form action="./index.php" method="post" accept-charset="utf-8">
		    <h2>Change Password</h2>
		      <div class="form-item" <?php echo (isset($reg_errors['password']) ? 'style="color: #ff0000;"' : ''); ?>>
		        <label for="password">Password</label>
		        <input type="password" maxlength="20" name="password" id="password" 
			     value="<?php echo (isset($clean['password']) && empty($reg_errors['password']) ? $clean['password'] : ''); ?>" >
	          </div>
	          <div class="form-item" <?php echo (isset($reg_errors['password1']) ? 'style="color: #ff0000;"' : ''); ?>>
		        <label for="password1">Verify Password</label>
		        <input type="password" maxlength="20" name="password1" id="password1">
	          </div>
	          <div class="form-item">
		        <button type="submit" name="password_change" value="1">Submit</button>
	          </div>
		    </form>
		  </div>
		</div>
<?php }
else {
	header('Location: /login/index.php');
	exit();
} ?>

<?php
include('../../../includes/footer.php');
?>