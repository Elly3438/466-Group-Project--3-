<?php /************************************
--Teacher School Page--
Author: Jeffrey Bowden
*****************************************/
require('../../../includes/common.php');
require('../../../includes/helpers.inc.php');
require('../../../includes/begin.php');

/***Processing the edit form submissions ***/
$form_inputs = array(); //Store sql form values
$reg_errors = array(); //keep track of any errors in the form
$form_values = array('name' => 'School Name', 'district' => 'District', 'street1' => 'Street 1', 'street2' => 'Street 2',
					'city' => 'City', 'zip' => 'Zip Code', 'state' => 'State', 'phone' => 'Phone Number');

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
	
	if(isset($_POST['add_school'])) {
	/** process the form values and retrieve any errors **/
		if(!empty($clean)){
			/** prepare phone number for processing **/
			$clean['phone'] = $clean['phone'];
			$clean['phone'] = preg_replace('/\D/', '', $clean['phone']);
			if(strlen($clean['phone']) == 11 && substr($clean['phone'], 0, 1) == 1){
				$clean['phone'] = substr($clean['phone'], 1);
			}
			foreach($clean as $key => $value){
				if(!empty($clean[$key])){
					$error = processInput($key, $value, $form_values);
					if($error != ''){
						$reg_errors[$key] = $error;
					}
				}
				else {
					if($key == 'street2'){
					
					}
					else{
						$reg_errors[$key] = 'You need to enter a '. $form_values[$key] .'<br />';
					}
				}
			}
		}
		if(empty($reg_errors)){
			/** add child info **/
		
			//prepare each value for secure sql entry
			foreach($clean as $key => $value){
				$form_inputs[$key] = mysqli_real_escape_string($dbc, $value);
			}
		
			$msg = addSchool($form_inputs); //add the school
		
			if(preg_match('/^Error/', $msg)){
				echo $msg;
			}
			else {
				echo $msg;
			}
		}
	}
	elseif(isset($_POST['update_school'])) {
		$school_id = mysqli_real_escape_string($dbc, $_POST['school']);

		$school_info = get_teach_school_info($account_info->u_id);
		if($school_info->num_rows == 0){
			$msg = updateSchool($account_info->u_id, $school_id, 'new'); //attach school to teacher
		}
		elseif($school_id == ''){

			close_school_rel($account_info->u_id);
			$msg = 'Removed current school.<br />';
		}
		else {
			$school_info = mysqli_fetch_object($school_info);
			if($school_info->school_id != $school_id){
				$msg = updateSchool($account_info->u_id, $school_id, 'update'); //update the school
			}
			else{
				$msg = '';
			}
		}
		
		if(preg_match('/^Error/', $msg)){
			echo $msg;
		}
		else {
			unset($clean);
			echo $msg;
		}
	}
	
}
include('../../../includes/header.php');
?>
<?php 
if(isset($_SESSION['username'])){
	$username = $_SESSION['username'];
	$usertype = get_user_type($_SESSION['username']);
?>

		<div id="tabs-wrapper">
			<ul class="tabs">
				<li class="tabs-click" ><a href="../">Account Overview</a></li>
				<li class="tabs-click" ><a href="../edit/">Edit Account Info</a></li>
				<li class="tabs-click active" ><a href="./">School Information</a></li>
				<li class="tabs-click" ><a href="../recommend/">Recommendations</a></li>
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
		  <div class="school-list-box" style="width:800px;"><h2>Change Current School</h2>
		  <?php
		  		$school_info = get_teach_school_info($account_info->u_id); 
		  		$school_list = build_school_list();
				if($school_info->num_rows == 0) {
					echo 'You currently are not assigned to a school.<br />'; ?>
					<div class="child-add-box">
			   		 	<form action="./index.php" method="post" accept-charset="utf-8">
			     		<h2>Change Your School</h2>
			     		<label for="school">School
		     			<span class="small">Update Your School</span></label>
						<select name="school" size="1" id="school">
						<option value>--</option>
						<?php
						foreach($school_list as $school_id => $school_name){
							echo '<option value="'. $school_id .'" >'. $school_name .'</option>';
						}
						?>
						</select>
						<div class="form-item">
			        		<button type="submit" name="update_school" value="1">Update</button>
		          		</div>
		     		</form></div>
				<?php }
				else { 
					$school_info = mysqli_fetch_object($school_info);
					echo 'You are currently teaching at '. $school_info->name .'<br />';
				?>
				<div class="child-add-box">
		   		 <form action="./index.php" method="post" accept-charset="utf-8">
		     		<h2>Change Your School</h2>
		     		<label for="school">School
	     			<span class="small">Update Your School</span></label>
					<select name="school" size="1" id="school">
					<option value>--</option>
					<?php
					foreach($school_list as $school_id => $school_name){
						$school_id == $school_info->school_id ? $schoolselect = 'selected' : $schoolselect = '';
						echo '<option value="'. $school_id .'" '. $schoolselect .'>'. $school_name .'</option>';
					}
					?>
					</select>
					<div class="form-item">
		        		<button type="submit" name="update_school" value="1">Update</button>
	          		</div>
		     	</form></div>
		    <?php } ?>
		  </div>
		  <div class="child-add-box">
		    <form action="./index.php" method="post" accept-charset="utf-8">
		     <h2>Add a School</h2>
		      <div class="form-item" <?php echo (isset($reg_errors['name']) ? 'style="color: #ff0000;"' : ''); ?>>
		        <label for="name">School Name
		        <span class="small">Enter the school name</span></label>
		        <input type="text" maxlength="100" name="name" id="name" 
			value="<?php echo (isset($clean['name']) ? $clean['name'] : ''); ?>" > 
	          </div>
	          <div class="form-item" <?php echo (isset($reg_errors['district']) ? 'style="color: #ff0000;"' : ''); ?>>
		        <label for="district">District
		        <span class="small">Enter the school district</span></label>
		        <input type="text" maxlength="50" name="district" id="district" 
			     value="<?php echo (isset($clean['district']) ? $clean['district'] : ''); ?>" > 
	          </div>
	          <div class="form-item" <?php echo (isset($reg_errors['street1']) ? 'style="color: #ff0000;"' : ''); ?>>
				<label for="street1" >Street 1
				<span class="small">Enter the street address</span></label>
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
				<span class="small">Add the city</span></label>
				<input type="text" maxlength="30" name="city" id="city" 
					value="<?php echo (isset($clean['city']) ? $clean['city'] : ''); ?>" > 
			  </div>
			  <div class="form-item" <?php echo (isset($reg_errors['zip']) ? 'style="color: #ff0000;"' : ''); ?>>
				<label for="zip">Zip
				<span class="small">Add the mailing code</span></label>
				<input type="text" maxlength="10" name="zip" id="zip" 
					value="<?php echo (isset($clean['zip']) ? $clean['zip'] : ''); ?>" > 
			  </div>
			  <div class="form-item" <?php echo (isset($reg_errors['state']) ? 'style="color: #ff0000;"' : ''); ?>>
				<label for="state">State
				<span class="small">Add the state</span></label>
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
				<span class="small">Add the school number</span></label>
				<input type="text" maxlength="20" name="phone" id="phone" 
					value="<?php echo (isset($clean['phone']) ? $clean['phone'] : ''); ?>" >
			  </div>
	          <div class="form-item">
		        <button type="submit" name="add_school" value="1">Add School</button>
	          </div>
		    </form>
		  </div>
		</div>
<?php }
else {
	header('Location: '.BASE_URL.'/login/index.php');
	exit();
} ?>

<?php
include('../../../includes/footer.php');
?>