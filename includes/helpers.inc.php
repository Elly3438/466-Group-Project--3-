<?php
/** PHP functions go in here **/

/** Checks to see if email is already in use for user registration
 *  Arguments: value to check for in the database, and which column it is in
 * Author: Jeffrey Bowden
 */

function form_validate_unique($value, $option){
	global $dbc;
	$query = 'SELECT * FROM user WHERE '. $option .' = \''. mysqli_real_escape_string($dbc, $value) .'\';';
	$result = mysqli_query($dbc, $query);
	return($result->num_rows == 0);
}

/** Adds a User to the database
 *  Arguments: An array of input values
 * Author: Jeffrey Bowden
 */
function addUser($arr){
	global $dbc;
	$encrypted = crypt(md5($arr['password']),md5($arr['username']));

	$query = 'INSERT INTO user (
				username,
				password,
				firstname,
				lastname,
				email,
				street1,
				street2,
				city,
				zip,
				state,
				phone,
				type,
				auth_token
			)
			VALUES (
				\''. $arr['username'] .'\',
				\''. $encrypted .'\',
				\''. $arr['firstname'] .'\',
				\''. $arr['lastname'] .'\',
				\''. $arr['email'] .'\',
				\''. $arr['street1'] .'\',
				\''. $arr['street2'] .'\',
				\''. $arr['city'] .'\',
				\''. $arr['zip'] .'\',
				\''. $arr['state'] .'\',
				\''. $arr['phone'] .'\',
				\''. $arr['type'] .'\',
				\'\' )';
	
	$result = mysqli_query($dbc, $query);
	if(!$result){
		$msg = 'Error - not added to database'. mysqli_errno($dbc) .'<br />';
	}
	else {
		$msg = 'Successfully added'. $arr['username'] .'to the database!<br />';
	}
	
	return $msg;
}

/** Sets logged in status 
 *  Arguments: the User's username and password, and if the remember box was checked
 * Author: Jeffrey Bowden
 */
function setLoggedIn($username, $password, $remember){
	global $dbc;
	$encrypted = crypt(md5($password),md5($username));
	
	$query = 'SELECT username, password, firstname, type FROM user WHERE username = \''. $username .'\' AND password = \''. $encrypted .'\';';
	$result = mysqli_query($dbc, $query);
	if($result->num_rows == 1){
		$userrow = mysqli_fetch_object($result);
		
		//encrypt cookie name
		$cookiename = md5(COOKIE_NAME);
		//generate an auth token for logged in state
		$auth_token = generate_auth_token($userrow->username);
		
		if(preg_match('/^Error/', $auth_token)){
			return false;
		}
		
		//store session data		
		$_SESSION['user'] = $userrow->username;
		$_SESSION['firstname'] = $userrow->firstname;
		
		//store cookie data
		if($remember){
			setcookie($cookiename, $auth_token, time()+(7 * 24 * 60 * 60), '/', 'groupproject.localhost');
		}
		else{
			setcookie($cookiename, $auth_token, false, '/', 'groupproject.localhost');
		}
		
		return true;
	}
	else {
		return false;
	}
	
}

/** Generates an auth token for the logged in user for secure cookie storage
 *  Arguments: the User's username
 * Author: Jeffrey Bowden
 */
function generate_auth_token($username){
	global $dbc;
	
	//generate random token
	$random_token = md5($username).'_';
	$random_token .= hash('sha256', uniqid(mt_rand(), true).uniqid(mt_rand(), true));

	//update user's auth token
	$query = 'UPDATE user SET '.
			 'auth_token = \''. $random_token .'\' '.
			 'WHERE username = \''. $username .'\'';
	
	$result = mysqli_query($dbc, $query);
	
	if(!$result){
		$msg = 'Error - problem generating token '. mysqli_errno($dbc) .'<br />';
		return $msg;
	}
	else {
		return $random_token;
	}
	
}

/** Removes the user's auth token from the db
 *  Arguments: the User's username
 * Author: Jeffrey Bowden
 */
function remove_auth_token($username){
	global $dbc;
	
	//remove the user's auth token from the db
	$query = 'UPDATE user SET '.
			 'auth_token = \'\''.
			 'WHERE username = \''. $username .'\'';
	
	$result = mysqli_query($dbc, $query);
	
	if(!$result){
		$msg = 'Error - problem removing token '. mysqli_errno($dbc) .'<br />';
		return $msg;
	}
	else {
		return '';
	}
}

/** Processes input from the register and edit forms
 *  Arguments: the key and values of the input array and the form's display values
 * Author: Jeffrey Bowden
 */
function processInput($key, $value, $form_values){
	$regular = '';
	$regular2 = '';
	
		switch ($key){
			case 'firstname':
			case 'lastname':
			case 'city':
			case 'state':
				$regular = '/^[A-Z \'.-]+$/i';
				break;
			case 'name':
			case 'district':
			case 'street1':
			case 'street2':
				$regular = '/^[A-Z0-9 \'.-]+$/i';
				break;
			case 'zip':
				$regular = '/^([0-9]{5})(-[0-9]{4})?$/i';
				break;
			case 'phone':
				$regular = '/^([0-9]{10})$/i';
				$regular2 = '/^([0-9]{7})$/i';
				break;
			case 'username':
				$regular = '/^[A-Z0-9]{4,20}$/i';
				break;
			case 'password':
				$regular = '/^(\w*(?=\w*\d)(?=\w*[a-z])(?=\w*[A-Z])\w*){6,20}$/';
				break;			
		}
		return(getError($key, $value, $regular, $regular2, $form_values));

}
/** checks form values and returns an error if there is one
 *  Arguments: the key, the value, the regular expressions, and form values
 * Author: Jeffrey Bowden
  **/
function getError($key, $value, $regular, $regular2, $form_values){
	if($key == 'email'){
		if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
			if(form_validate_unique($value, $key)){
				return '';
			}
			else{
				return $form_values[$key] .' already in use.<br />';
			}
		}
		else {
			return 'Please enter a valid '. $form_values[$key] .'.<br />';
		}
	}
	elseif($key == 'username'){
		if (preg_match($regular, $value)) {
			if(form_validate_unique($value, $key)){
				return '';
			}
			else{
				return $form_values[$key] .' already in use.<br />';
			}
		}
		else {
			return 'Please enter a valid '. $form_values[$key] .'(4-20 characters long).<br />';
		}
	}
	elseif($key == 'phone'){
		$value = preg_replace('/\D/', '', $value);
		if(strlen($value) == 11 && substr($value, 0, 1) == 1){
			$value = substr($value, 1);
		}
		if(preg_match($regular, $value)){
			return '';			
		}
		elseif(preg_match($regular2, $value)){
			return 'You need to enter an area code for your '. $form_values[$key] .'<br />';
		}
		else {
			return $form_values[$key] .' needs to be valid characters and format 888-888-8888<br />';
		}
	}
	elseif($key == 'password1' || $key == 'new-registration' || $key == 'user' || $key == 'address_change' || $key == 'info_change' || $key == 'add_school' || $key == 'password_change'){
		return '';
	}
	else {
		if(preg_match($regular, $value)){
			return '';		
		}
		else {
			if($key == 'zip'){
				return $form_values[$key] .' needs to be 5 digits, or in the format 12345-1234<br />';
			}
			elseif($key == 'password'){
				return 'Your '. $form_values[$key] .' is not valid (Must be 6-20 characters long, contain at least one caps character and one number).<br />';
			}
			else{
				return $form_values[$key] .' needs to be valid characters.<br />';
			}
			
		}
	}
	
	
}

/** Processes the login and returns any errors
 *  Arguments: the login form and errors array
 * Author: Jeffrey Bowden
 */
function processLogin($clean, $login_errors){
	global $dbc;
	
	if(empty($clean['username'])){
		$login_errors['username'] = 'You need to enter a valid Username.<br />';
	}
	if(empty($clean['password'])){
		$login_errors['password'] = 'You need to enter a Password.<br />';
	}
	if(empty($login_errors)){
		$clean['username'] = mysqli_real_escape_string($dbc, $clean['username']);
		$clean['password'] = mysqli_real_escape_string($dbc, $clean['password']);
		if(isset($clean['remember'])){
			$clean['remember'] = true;
		}
		else{
			$clean['remember'] = false;
		}
		
		if(form_validate_unique($clean['username'], 'username')){
			$login_errors['login-error'] = 'Username not in the system - please enter it again.<br />';
		}
		elseif(!setLoggedIn($clean['username'], $clean['password'], $clean['remember'])){
			$login_errors['login_error'] = 'Password is incorrect - please enter it again. <br />';
		}
	}
	
	return $login_errors;
}

/** returns what type the current user is
 *  Arguments: the User's username
 * Author: Jeffrey Bowden
 */
function get_user_type($username){
	global $dbc;
	
	$query = 'SELECT type '.
			 'FROM user '.
			 'WHERE username = \''. $username .'\'';
	
	$result = mysqli_query($dbc, $query);
	if(!$result){
		return 0;
	}
	else {
		$row = mysqli_fetch_object($result);
		return $row->type;
	}
}

/** returns info for the current user for the my accounts page
 *  Arguments: the User's username, and User's type
 * Author: Jeffrey Bowden
 */
function get_user_acc_info($username){
	global $dbc;
	
	$query = 'SELECT u_id, firstname, lastname, email, street1, street2, city, zip, state, phone '.
			 'FROM user '.
			 'WHERE username = \''. $username .'\'';
	
	$result = mysqli_query($dbc, $query);
	if(!$result){
		return 0;
	}
	else {
		$row = mysqli_fetch_object($result);
		return $row;
	}
}

/** returns child info for the current user for the my accounts page
 *  Arguments: the User's username
 * Author: Jeffrey Bowden
 */
function get_user_child_info($id, $type){
	global $dbc;
	
	if ($type == 'user'){
		$query = 'SELECT child_id, firstname, lastname, age '.
				 'FROM child '.
				 'WHERE u_id = \''. $id .'\' '.
				 'AND active = 1';
	}
	elseif ($type == 'child'){
		$query = 'SELECT child_id, firstname, lastname, age '.
				 'FROM child '.
				 'WHERE child_id = \''. $id .'\' '.
				 'AND active = 1';
	}
	elseif ($type == 'teacher'){
		$query = 'SELECT firstname, lastname, age '.
				 'FROM child AS a '.
				 'INNER JOIN teacher_child_rel AS b '.
				 'USING(child_id) '.
				 'WHERE teacher_u_id = \''. $id .'\' '.
				 'AND DATE(b.date_end) = \'0000-00-00\' '.
				 'AND a.active = 1';
	}
	$result = mysqli_query($dbc, $query);
	if(!$result){
		die('Error - '. mysqli_errno($dbc));
	}
	else {
		return $result;
	}
}

/** updates User's information
 *  Arguments: the User's username and an array containing the form inputs
 * Author: Jeffrey Bowden
 */
function updateUser($username, $form_inputs){
	global $dbc;
	
	$query = 'UPDATE user SET ';
	foreach($form_inputs as $key => $value){
		$query .= $key .' = \''. $value .'\', '; 
	}
	$query = substr($query, 0, -2);
	$query .= ' WHERE username = \''. $username .'\';';
	
	$result = mysqli_query($dbc, $query);
	if(!$result){
		$msg = 'Error - not updated '. mysqli_errno($dbc) .'<br />';
	}
	else {
		$msg = 'Successfully updated your information';
	}
	return $msg;
}

/** adds a child to the database
 *  Arguments: the User's username
 * Author: Jeffrey Bowden
 */
function addChild($u_id, $arr){
	global $dbc;
	
	$query = 'INSERT INTO child (
			    u_id,
			    firstname,
			    lastname,
			    age,
			    active
			  )
			  VALUES (
			    \''. $u_id .'\',
			    \''. $arr['firstname'] .'\',
			    \''. $arr['lastname'] .'\',
			    \''. $arr['age'] .'\',
			    1 )';
	
	$result = mysqli_query($dbc, $query);
	if(!$result){
		$msg = 'Error - not added to database'. mysqli_errno($dbc) .'<br />';
	}
	else {
		$msg = 'Successfully added '. $arr['firstname'] .' to the database!<br />';
	}
	
	return $msg;
}

/** builds an array of the current teachers in the system
 *  Arguments: none
 * Author: Jeffrey Bowden
 */
function build_teacher_list(){
	global $dbc;
	
	$query = 'SELECT u_id, firstname, lastname '.
			 'FROM user '.
			 'WHERE type = 3;';
	
	$result = mysqli_query($dbc, $query);
	if(!$result){
		die ('Error '. mysqli_errno($dbc) .'<br />');
	}
	else {
		$temparr = array();
		while($row = mysqli_fetch_object($result)){
			$temparr[$row->u_id] = $row->firstname .' '. $row->lastname;
		}
		return $temparr;
	}
}
/** builds an array of the current schools in the system
 *  Arguments: none
 * Author: Jeffrey Bowden
 */
function build_school_list(){
	global $dbc;
	
	$query = 'SELECT school_id, name '.
			 'FROM school;';
	
	$result = mysqli_query($dbc, $query);
	if(!$result){
		die ('Error '. mysqli_errno($dbc) .'<br />');
	}
	else {
		$temparr = array();
		while($row = mysqli_fetch_object($result)){
			$temparr[$row->school_id] = $row->name;
		}
		return $temparr;
	}
}
/** updates child information
 *  Arguments: the User's username
 * Author: Jeffrey Bowden
 */
function updateChild($child, $form_inputs){
	global $dbc;
	
	$query = 'UPDATE child SET ';
	foreach($form_inputs as $key => $value){
		$query .= $key .' = \''. $value .'\', '; 
	}
	$query = substr($query, 0, -2);
	$query .= ' WHERE child_id = \''. $child .'\';';
	
	$result = mysqli_query($dbc, $query);
	if(!$result){
		$msg = 'Error - not updated '. mysqli_errno($dbc) .'<br />';
	}
	else {
		$msg = 'Successfully updated your information';
	}
	return $msg;
}
/** updates child and teacher relation
 *  Arguments: user id, child id, and teacher id
 * Author: Jeffrey Bowden
 */
function updateChildTeacher($u_id, $child_id, $teacher_id, $oldteacher){
	global $dbc;
	
	$query = 'UPDATE teacher_child_rel SET '.
				 'date_end = CURDATE() '.
				 'WHERE child_id = \''. $child_id .'\''.
				 'AND DATE(date_end) = \'0000-00-00\';';
	$result = mysqli_query($dbc, $query);
	if(!$result){
		$msg = 'Error - '. mysqli_errno($dbc) .'<br />';
	}
	else{
		if($teacher_id != ''){
			$query = 'INSERT INTO teacher_child_rel (
				    teacher_u_id,
				    u_id,
				    child_id,
				    date_start,
				    date_end
				  )
				  VALUES (
				    \''. $teacher_id .'\',
				    \''. $u_id .'\',
				    \''. $child_id .'\',
				    CURDATE(),
				    \'0000-00-00\' )';
			
			$result = mysqli_query($dbc, $query);
			if(!$result){
				$msg = 'Error - not updated '. mysqli_errno($dbc) .'<br />';
			}
			else {
				$msg = 'Successfully updated your information<br />';
			}
		}
		else{
			$msg = 'Successfully updated your information<br />';
		}
	}
	return $msg;
}
/** returns the child's current teacher
 *  Arguments: child id
 * Author: Jeffrey Bowden
 */
function get_child_teacher($child){
	global $dbc;
	
	$query = 'SELECT teacher_u_id '.
			 'FROM teacher_child_rel '.
			 'WHERE child_id = \''. $child .'\' '.
			 'AND DATE(date_end) = \'0000-00-00\';';
	
	$result = mysqli_query($dbc, $query);
	if(!$result){
		die ('Error '. mysqli_errno($dbc) .'<br />');
	}
	else{
		if($result->num_rows != 0){
			$row = mysqli_fetch_object($result);
			return $row->teacher_u_id;
		}
		else{
			return '';
		}
	}
}
/** returns child's id number
 *  Arguments: none
 * Author: Jeffrey Bowden
 */
function deactivateChild($child_id){
	global $dbc;
	
	$query = 'UPDATE child SET '.
			 'active = 0 '.
			 'WHERE child_id = \''. $child_id .'\';';
	
	$result = mysqli_query($dbc, $query);
	if(!$result){
		$msg = 'Error - child not deleted '. mysqli_errno($dbc) .'<br />';
	}
	else {
		$msg = 'Successfully removed child<br />';
	}
	return $msg;
}
/** returns child's instrument information
 *  Arguments: none
 * Author: Jeffrey Bowden
 */
function get_current_child_inst_info($child_id){
	global $dbc;
	
	$query = 'SELECT name, order_merch_id, serial_num, rent_start_date, time_period, rental_total, total_rent_applied '.
			 'FROM order_merch_item AS a '.
			 'INNER JOIN merch_item AS b ON a.rent_merch_id = b.merch_id '.
			 'WHERE child_id = \''. $child_id .'\' '.
			 'AND DATE(rent_end_date) = \'0000-00-00\';';
	
	$result = mysqli_query($dbc, $query);
	if(!$result){
		die('Error - '. mysqli_errno($dbc));
	}
	else {
		return $result;
	}
}
/** returns child's instrument information
 *  Arguments: none
 * Author: Jeffrey Bowden
 */
function deactivateInst($order_merch_id){
	global $dbc;
	
	$query = 'UPDATE order_merch_item SET '.
			 'rent_end_date = CURDATE() '.
			 'WHERE order_merch_id = \''. $order_merch_id .'\';';
	
	$result = mysqli_query($dbc, $query);
	if(!$result){
		$msg = 'Error - instrument not cancelled '. mysqli_errno($dbc) .'<br />';
	}
	else {
		$msg = 'Successfully cancelled instrument<br />';
	}
	return $msg;
	
}
/** returns user's general order information
 *  Arguments: user's id
 * Author: Jeffrey Bowden
 */
function get_cust_order_info($u_id){
	global $dbc;
	
	$query = 'SELECT * FROM cust_order '.
			 'WHERE u_id = \''. $u_id .'\';';
	
	$result = mysqli_query($dbc, $query);
	if(!$result){
		die('Error - '. mysqli_errno($dbc));
	}
	else{
		return $result;
	}
}
/** returns total purchases for an order
 *  Arguments: user's id
 * Author: Jeffrey Bowden
 */
function get_sum_purchases($order_id){
	global $dbc;
	
	$query = 'SELECT COUNT(*) AS total '.
			 'FROM order_merch_item '.
			 'WHERE order_id = \''. $order_id .'\' '.
			 'AND purch_merch_id IS NOT NULL ;';
	
	$result = mysqli_query($dbc, $query);
	if(!$result){
		die('Error - '. mysqli_errno($dbc));
	}
	else{
		$row = mysqli_fetch_object($result);
		return $row->total;
	}
	
}
/** returns total rentals for an order
 *  Arguments: user's id
 * Author: Jeffrey Bowden
 */
function get_sum_rentals($order_id){
	global $dbc;
	
	$query = 'SELECT COUNT(*) AS total '.
			 'FROM order_merch_item '.
			 'WHERE order_id = \''. $order_id .'\' '.
			 'AND rent_merch_id IS NOT NULL ;';
	
	$result = mysqli_query($dbc, $query);
	if(!$result){
		die('Error - '. mysqli_errno($dbc));
	}
	else{
		$row = mysqli_fetch_object($result);
		return $row->total;
	}
}
/** returns school information for a teacher
 *  Arguments: user's id
 * Author: Jeffrey Bowden
 */
function get_teach_school_info($u_id){
	global $dbc;
	
	$query = 'SELECT a.school_id, name, district, street1, street2, city, zip, state, phone, date_start '.
			 'FROM school AS a '.
			 'INNER JOIN teach_school_rel AS b '.
			 'USING(school_id) '.
			 'WHERE b.u_id = \''. $u_id .'\' '.
			 'AND DATE(b.date_end) = \'0000-00-00\';';
	
	$result = mysqli_query($dbc, $query);
	if(!$result){
		die('Error - '. mysqli_errno($dbc));
	}
	else{
		return $result;
	}
	
}
/** Adds a school to the database
 *  Arguments: An array of input values
 * Author: Jeffrey Bowden
 */
function addSchool($arr){
	global $dbc;

	$query = 'INSERT INTO school (
				name,
				district,
				street1,
				street2,
				city,
				zip,
				state,
				phone
			)
			VALUES (
				\''. $arr['name'] .'\',
				\''. $arr['district'] .'\',
				\''. $arr['street1'] .'\',
				\''. $arr['street2'] .'\',
				\''. $arr['city'] .'\',
				\''. $arr['zip'] .'\',
				\''. $arr['state'] .'\',
				\''. $arr['phone'] .'\' )';
	
	$result = mysqli_query($dbc, $query);
	if(!$result){
		$msg = 'Error - not added to database'. mysqli_errno($dbc) .'<br />';
	}
	else {
		$msg = 'Successfully added'. $arr['name'] .'to the database!<br />';
	}
	
	return $msg;
}
/** updates a teacher's current school
 *  Arguments: user's id, school id and if the update is new or update
 *  Author: Jeffrey Bowden
 */
function close_school_rel($u_id){
	global $dbc;
	
	$query = 'UPDATE teach_school_rel SET '.
			 'date_end = CURDATE() '.
			 'WHERE u_id = \''. $u_id .'\' '.
			 'AND DATE(date_end) = \'0000-00-00\';';
	
	$result = mysqli_query($dbc, $query);
	if(!$result){
		die('Error - '. mysqli_errno($dbc) .'<br />');
	}
}
/** updates a teacher's current school
 *  Arguments: user's id, school id and if the update is new or update
 *  Author: Jeffrey Bowden
 */
function updateSchool($u_id, $school_id, $type){
	global $dbc;
	
	if($type =='update'){
		close_school_rel($u_id);
	}
	$query = 'INSERT INTO teach_school_rel (
					u_id,
					school_id,
					date_start,
					date_end
				 )
				 VALUES (
				 	\''. $u_id .'\',
				 	\''. $school_id .'\',
				 	CURDATE(),
				 	\'0000-00-00\' )';
	
	$result = mysqli_query($dbc, $query);
	if(!$result){
		$msg = 'Error - not added to database'. mysqli_errno($dbc) .'<br />';
	}
	else {
		$msg = 'Successfully updated your current school.<br />';
	}
	
	return $msg;
	
}
/** returns the teacher's current recommended products
 *  Arguments: user's id
 *  Author: Jeffrey Bowden
 */
function get_teach_recommend($u_id){
	global $dbc;
	
	$query = 'SELECT a.merch_id, name '.
			 'FROM teach_req AS a '.
			 'INNER JOIN merch_item AS b '.
			 'USING(merch_id) '.
			 'WHERE a.u_id = \''. $u_id .'\';';
	
	$result = mysqli_query($dbc, $query);
	if(!$result){
		die('Error - '. mysqli_errno($dbc));
	}
	else{
		return $result;
	}
	
}
/** deletes a teacher/acc relation
 *  Arguments: user's id and merch id to remove
 *  Author: Jeffrey Bowden
 */
function deleteTeachAcc($u_id, $merch_id){
	global $dbc;
	
	$query = 'DELETE FROM teach_req '.
			 'WHERE u_id = \''. $u_id .'\' '.
			 'AND merch_id = \''. $merch_id .'\';';
	
	$result = mysqli_query($dbc, $query);
	if(!$result){
		die('Error - '. mysqli_errno($dbc));
	}
	
}
/** returns an accessory list for a teacher to add accessories from
 *  Arguments: user's id
 *  Author: Jeffrey Bowden
 */
function build_acc_list($u_id){
	global $dbc;
	
	$query = 'SELECT merch_id, name '.
			 'FROM merch_item AS a '.
			 'WHERE a.category_name = \'Accessory\' '.
			 'AND NOT EXISTS ( '.
				'SELECT merch_id FROM teach_req AS b '.
				'WHERE a.merch_id = b.merch_id AND b.u_id = \''. $u_id .'\' ) ';
	
	$result = mysqli_query($dbc, $query);
	if(!$result){
		die ('Error '. mysqli_errno($dbc) .'<br />');
	}
	else {
		$temparr = array();
		while($row = mysqli_fetch_object($result)){
			$temparr[$row->merch_id] = $row->name;
		}
		return $temparr;
	}
}
/** adds an accessory to a teacher's recommended list
 *  Arguments: user's id, merch id
 *  Author: Jeffrey Bowden
 */
function addAcc($u_id, $merch_id){
	global $dbc;
	
	$query = 'INSERT INTO teach_req (
				u_id,
				merch_id
			  )
			  VALUES (
			  	\''. $u_id .'\',
			  	\''. $merch_id .'\' )';
	
	$result = mysqli_query($dbc, $query);
	if(!$result){
		$msg = 'Error - not added to database'. mysqli_errno($dbc) .'<br />';
	}
	else {
		$msg = 'Successfully updated your current school.<br />';
	}
	
	return $msg;
}
/** Get's product info
 *  Arguments: The product ID
 *  Author: Lila Papiernik
 */
function get_product_info($product_id){
	global $dbc;
	
	$query = 'SELECT * '.
			 'FROM merch_item '.
			 'WHERE merch_id = \''. $product_id .'\';';
	
	$result = mysqli_query($dbc, $query);
	if(!$result){
		die ('Error '. mysqli_errno($dbc) .'<br />');
	}
	else{
		if($result->num_rows != 0){
			$row = mysqli_fetch_object($result);
			return $row;
		}else{
			return NULL;
		}
	}
}
?>