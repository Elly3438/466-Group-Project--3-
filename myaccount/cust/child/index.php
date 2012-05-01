<?php /************************************
--Customer Child Page--
Author: Jeffrey Bowden
*****************************************/
require('../../../includes/common.php');
require('../../../includes/helpers.inc.php');
require('../../../includes/begin.php');

/***Processing the edit form submissions ***/
$form_inputs = array(); //Store sql form values
$reg_errors = array(); //keep track of any errors in the form
$form_values = array('firstname' => 'First Name', 'lastname' => 'Last Name', 'age' => 'Age');

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
	
	if(isset($_POST['add_child'])) {
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
					if($key =='age'){
						$reg_errors[$key] = 'You need to enter an '. $form_values[$key] .'<br />';
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
		
			$msg = addChild($account_info->u_id, $form_inputs); //add the child
		
			if(preg_match('/^Error/', $msg)){
				echo $msg;
			}
			else {
				unset($clean);
				echo $msg;
			}
		}
	}
	elseif(isset($_POST['delete_child'])) {
		$child_id = mysqli_real_escape_string($dbc, $_POST['child']);
		
		$msg = deactivateChild($child_id); //deactivate the child

		if(preg_match('/^Error/', $msg)){
			echo $msg;
		}
		else {
			unset($clean);
			echo $msg;
		}
	}
	elseif(isset($_POST['update_child'])) {
		unset($clean['update_child']);
		
		$child_id = mysqli_real_escape_string($dbc, $clean['child']);
		
		unset($clean['child']);
		
		$child = get_user_child_info($child_id, 'child');
		
		$child = mysqli_fetch_object($child);
		
		$teacher_id = mysqli_real_escape_string($dbc, $clean['teacher']);
		$current_teach = get_child_teacher($child_id);
		$msg = '';
		if($teacher_id != $current_teach){
			$msg = updateChildTeacher($account_info->u_id, $child_id, $teacher_id, $current_teach);
		}

		unset($clean['teacher']);
		
		foreach($clean as $key => $value){
			if($value == $child->$key){
				unset($clean[$key]);
			}
		}
		
		/** process the form values and retrieve any errors **/
		if(!empty($clean)){
			foreach($clean as $key => $value){
				if(!empty($clean[$key])){
					echo $key .' | ';
					echo $value;
					$error = processInput($key, $value, $form_values);
					if($error != ''){
						$reg_errors[$key] = $error;
					}
				}
				else {
					if($key =='age'){
						$reg_errors[$key] = 'You need to enter an '. $form_values[$key] .'<br />';
					}
					else{
						$reg_errors[$key] = 'You need to enter a '. $form_values[$key] .'<br />';
					}
				}
			}
		}
		if(empty($reg_errors) && !empty($clean)){
			/** update child info **/
		
			//prepare each value for secure sql entry
			foreach($clean as $key => $value){
				$form_inputs[$key] = mysqli_real_escape_string($dbc, $value);
			}
			$msg .= updateChild($child_id, $form_inputs); //update the child

			if(preg_match('/^Error/', $msg)){
				echo $msg;
			}
			else {
				unset($clean);
				echo $msg;
			}
		}
	}
	elseif(isset($_POST['inst_cancel'])) {
		$order_merch_id = mysqli_real_escape_string($dbc, $_POST['order_merch_id']);
		
		$msg = deactivateInst($order_merch_id); //deactivate the instrument

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
				<li class="tabs-click" ><a href="../orders/">Order History</a></li>
				<li class="tabs-click active" ><a href="./">Child Info</a></li>
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
		  <div class="child-list-box" style="width:800px; float: left"><h2>Edit Child Information</h2>
		  <?php
		  		$child_info = get_user_child_info($account_info->u_id, 'user'); 
				if($child_info->num_rows == 0) {
					echo 'You currently do not have any children registered.';
				}
				else { 
				$teacher_list = build_teacher_list(); 
				$i = 1; ?>
				<ul style="list-style-type: none; padding: 0; float: left;">
					<?php while($row = mysqli_fetch_object($child_info)){ ?>
					<li>
					<form action="./index.php" id="child-update" method="post" accept-charset="utf-8">
					  <ul style="list-style-type: none; float: left; padding: 0;">
					    <li style="float: left">
					      <label for="firstname<?php echo $i; ?>" <?php #echo (isset($reg_errors['firstname']) ? 'style="color: #ff0000;"' : ''); ?>>First Name</label>
		        		  <input type="text" maxlength="30" name="firstname" id="firstname<?php echo $i; ?>" 
						  value="<?php echo $row->firstname; ?>" ></li>
						<li>
						  <label for="lastname<?php echo $i; ?>" <?php #echo (isset($reg_errors['firstname']) ? 'style="color: #ff0000;"' : ''); ?>>Last Name</label>
		        		  <input type="text" maxlength="30" name="lastname" id="lastname<?php echo $i; ?>" 
						  value="<?php echo $row->lastname; ?>" ></li>
						<li>
						  <label for="age<?php echo $i; ?>" <?php #echo (isset($reg_errors['firstname']) ? 'style="color: #ff0000;"' : ''); ?>>Age</label>
		        		  <input type="text" maxlength="2" name="age" id="age<?php echo $i; ?>" 
						  value="<?php echo $row->age; ?>" ></li>
						<li>
						  <label for="teacher<?php echo $i; ?>">Teacher</label>
							<select name="teacher" size="1" id="teacher<?php echo $i; ?>">
							<option value>--</option>
							<?php $teacher_id = get_child_teacher($row->child_id);
							foreach($teacher_list as $teach_id => $teach_name){
								$teacher_id == $teach_id ? $teachselect = 'selected' : $teachselect = '';
								echo '<option value="'. $teach_id .'" '. $teachselect .'>'. $teach_name .'</option>';
							}
							?>
							</select>
							<input type="hidden" name="child" id="child"
							value="<?php echo $row->child_id; ?>"></li>
						<li>
		        			<button type="submit" name="delete_child" value="1">Delete</button></li>
		        			<li><button type="submit" name="update_child" value="1">Update</button></li>
					</ul></form></li>
					<li><div class="child-instrument">
					<h2>Current Instruments for <?php echo $row->firstname; ?></h2>
					<?php $child_inst_info = get_current_child_inst_info($row->child_id); 
						if($child_inst_info->num_rows != 0){
							while($irow = mysqli_fetch_object($child_inst_info)){ ?>
						<ul>
						<li>Instrument: <?php echo $irow->name; ?></li>
						<li>Serial Number: <?php echo $irow->serial_num;?></li>
						<li>Rental Start Date: <?php echo $irow->rent_start_date; ?></li>
						<li>Rental Rate: $<?php echo $irow->rental_total;?> per <?php echo $irow->time_period == 'M' ? 'Month' : 'Quarter'; ?></li>
						<li>Total Applied to this Instrument: $<?php echo $irow->total_rent_applied; ?></li>
						<li><form action="./index.php" id="inst-cancel" method="post" accept-charset="utf-8">
							<input type="hidden" name="order_merch_id" value="<?php echo $irow->order_merch_id; ?>">
							<button type="submit" name="inst_cancel" value="1">Cancel</button>
						</form></li>
						</ul>
						<?php }
						}
						else{
							echo 'No instruments';
						}
					?>
					</div></li>
					<?php $i++;
						} ?>
				</ul>
				<?php
				}
				?>
		  </div>
		  <div class="child-add-box" style="float: left">
		    <form action="./index.php" method="post" accept-charset="utf-8">
		     <h2>Add a Child</h2>
		      <div class="form-item" <?php #echo (isset($reg_errors['firstname']) ? 'style="color: #ff0000;"' : ''); ?>>
		        <label for="firstname">First Name</label>
		        <input type="text" maxlength="30" name="firstname" id="firstname" 
			value="<?php #echo (isset($clean['firstname']) ? $clean['firstname'] : ''); ?>" > 
	          </div>
	          <div class="form-item" <?php #echo (isset($reg_errors['lastname']) ? 'style="color: #ff0000;"' : ''); ?>>
		        <label for="firstname">Last Name</label>
		        <input type="text" maxlength="30" name="lastname" id="lastname" 
			     value="<?php #echo (isset($clean['lastname']) ? $clean['lastname'] : ''); ?>" > 
	          </div>
	          <div class="form-item" <?php #echo (isset($reg_errors['age']) ? 'style="color: #ff0000;"' : ''); ?>>
		        <label for="age">Age</label>
		        <input type="text" maxlength="2" name="age" id="age" 
			value="<?php #echo (isset($clean['age']) ? $clean['age'] : ''); ?>" > 
	          </div>
	          <div class="form-item">
		        <button type="submit" name="add_child" value="1">Add Child</button>
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