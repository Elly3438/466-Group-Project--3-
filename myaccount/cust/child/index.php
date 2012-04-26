<?php
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
			/** update user info **/
		
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
}
include('../../../includes/header.php');
?>
<?php 
if(isset($_SESSION['username'])){
	$username = $_SESSION['username'];
	$usertype = get_user_type($_SESSION['username']);
?>

<div class="myaccount-title">Customer Page</div>
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
		  		$child_info = get_user_child_info($account_info->u_id); 
				if($child_info->num_rows == 0) {
					echo 'You currently do not have any children registered.';
				}
				else { 
				$teacher_list = build_teacher_list(); 
				$i = 1; ?>
				<ul style="list-style-type: none; padding: 0">
					<?php while($row = mysqli_fetch_object($child_info)){ ?>
					<li style="height: 20px">
					<form action="./index.php" method="post" accept-charset="utf-8">
					  <ul style="list-style-type: none; padding: 0;">
					    <li style="float: left">
					      <label for="firstname" <?php echo (isset($reg_errors['firstname']) ? 'style="color: #ff0000;"' : ''); ?>>First Name</label>
		        		  <input style="width: 80px" type="text" maxlength="30" name="firstname" id="firstname" 
						  value="<?php echo (isset($clean['firstname']) ? $clean['firstname'] : $row->firstname); ?>" ></li>
						<li style="float: left; margin-left: 20px">
						  <label for="lastname" <?php echo (isset($reg_errors['firstname']) ? 'style="color: #ff0000;"' : ''); ?>>Last Name</label>
		        		  <input style="width: 80px" type="text" maxlength="30" name="lastname" id="lastname" 
						  value="<?php echo (isset($clean['lastname']) ? $clean['lastname'] : $row->lastname); ?>" ></li>
						<li style="float: left; margin-left: 20px">
						  <label for="age" <?php echo (isset($reg_errors['firstname']) ? 'style="color: #ff0000;"' : ''); ?>>Age</label>
		        		  <input style="width: 20px" type="text" maxlength="2" name="age" id="age" 
						  value="<?php echo (isset($clean['age']) ? $clean['age'] : $row->age); ?>" ></li>
						<li style="float: left; margin-left: 20px">
						  <label for="teacher">Teacher</label>
							<select name="teacher" size="1" id="teacher">
							<option value>--</option>
							<?php foreach($teacher_list as $teach_id => $teach_name){
							if(isset($clean['state'])){ $clean['state'] == $teach_name ? $teachselect = 'selected' : $teachselect = ''; }
							echo '<option value="'. $teach_id .'" '. $teachselect .'>'. $teach_name .'</option>';
							}
							?>
							</select>
						<li style="float: left; margin-left: 20px">
		        			<button type="submit" name="update_child" value="1">Delete</button></li>
		        		<li style="float: left; margin-left: 20px">
		        			<button type="submit" name="update_child" value="2">Update</button></li>
					</ul></form></li>
					<?php $i++;
						} ?>
				</ul>
				<?php
				}
				?>
		  </div>
		  <div class="child-add-box" style="float: left"><h2>Add a Child</h2>
		    <form action="./index.php" method="post" accept-charset="utf-8">
		      <div class="form-item" <?php echo (isset($reg_errors['firstname']) ? 'style="color: #ff0000;"' : ''); ?>>
		        <label for="firstname">First Name</label>
		        <input type="text" maxlength="30" name="firstname" id="firstname" 
			value="<?php echo (isset($clean['firstname']) ? $clean['firstname'] : ''); ?>" > 
	          </div>
	          <div class="form-item" <?php echo (isset($reg_errors['lastname']) ? 'style="color: #ff0000;"' : ''); ?>>
		        <label for="firstname">Last Name</label>
		        <input type="text" maxlength="30" name="lastname" id="lastname" 
			     value="<?php echo (isset($clean['lastname']) ? $clean['lastname'] : ''); ?>" > 
	          </div>
	          <div class="form-item" <?php echo (isset($reg_errors['age']) ? 'style="color: #ff0000;"' : ''); ?>>
		        <label for="age">Age</label>
		        <input type="text" maxlength="2" name="age" id="age" 
			value="<?php echo (isset($clean['age']) ? $clean['age'] : ''); ?>" > 
	          </div>
	          <div class="form-item">
		        <button type="submit" name="add_child" value="1">Add Child</button>
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