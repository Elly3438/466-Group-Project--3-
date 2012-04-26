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
	elseif($key == 'password1' || $key == 'new-registration' || $key == 'user' || $key == 'address_change' || $key = 'info_change' || $key == 'password_change'){
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
function get_user_child_info($userid){
	global $dbc;
	
	$query = 'SELECT firstname, lastname, age '.
			 'FROM child '.
			 'WHERE u_id = \''. $userid .'\'';
	
	$result = mysqli_query($dbc, $query);
	if(!$result){
		return 0;
	}
	else {
		return $result;
	}
}

/** returns child info for the current user for the my accounts page
 *  Arguments: the User's username
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
			    age
			  )
			  VALUES (
			    \''. $u_id .'\',
			    \''. $arr['firstname'] .'\',
			    \''. $arr['lastname'] .'\',
			    \''. $arr['age'] .'\' )';
	
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
 *  Arguments: the User's username
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
?>