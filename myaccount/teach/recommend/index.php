<?php
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
	
	if(isset($_POST['delete_acc'])) {
		unset($clean['delete_acc']);
		
		if(!empty($clean)){
			foreach($clean as $delete_id){
				deleteTeachAcc($account_info->u_id, $delete_id);
			}
		}
	}
	elseif(isset($_POST['add_acc'])) {
		
		$msg = addAcc($account_info->u_id, $clean['acc']);
		
		if(preg_match('/^Error/', $msg)){
			echo $msg;
		}
		else {
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
				<li class="tabs-click" ><a href="../school/">School Information</a></li>
				<li class="tabs-click active" ><a href="./">Recommendations</a></li>
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
		  <div class="school-list-box" style="width:800px; float: left"><h2>Current Recommended Accessories</h2>
		  <?php
		  		$recommend = get_teach_recommend($account_info->u_id);
		  		if($recommend->num_rows == 0){
		  			echo 'No accessories recommended';
		  		}
		  		else{
		  			$i = 1; ?>
		  			<form action="./index.php" method="post" accept-charset="utf-8">
		  			<table style="text-align: center">
		  				<tr>
		  					<th style="width: 100px">Delete</th>
		  					<th style="width: 400px">Name</th>
		  				</tr>
		  		<?php while($row = mysqli_fetch_object($recommend)){ ?>
		  				<tr>
		  					<td><input type="checkbox" name="delete<?php echo $i; ?>" value="<?php echo $row->merch_id; ?>" ></input></td>
		  					<td><?php echo $row->name; ?></td>
		  				</tr>
		  		<?php $i++; } ?>
			  		</table>
			  		<div class="form-item">
		        		<button type="submit" name="delete_acc" value="1">Submit</button>
	          		</div>
			  		</form>
			<?php }
		  
		  		$teach_acc_list = build_acc_list($account_info->u_id); ?>
					<div class="child-add-box">
			   		 	<form action="./index.php" method="post" accept-charset="utf-8">
			     		<h2>Recommend an Accessory</h2>
			     		<label for="acc">Accessory
		     			<span class="small">Select an accessory</span></label>
						<select name="acc" size="1" id="acc">
						<option value>--</option>
						<?php
						foreach($teach_acc_list as $merch_id => $name){
							echo '<option value="'. $merch_id .'" >'. $name .'</option>';
						}
						?>
						</select>
						<div class="form-item">
			        		<button type="submit" name="add_acc" value="1">Add</button>
		          		</div>
		     		</form></div>
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