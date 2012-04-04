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
				type
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
				\''. $arr['type'] .'\')';
	
	$result = mysqli_query($dbc, $query);
	if(!$result){
		$msg = 'Error - not added to database'. mysqli_errno() .'<br />';
	}
	else {
		$msg = 'Successfully added'. $arr['username'] .'to the database!<br />';
	}
	
	return $msg;
}

/** Sets logged in status 
 *  Arguments: the User's username and password
 * Author: Jeffrey Bowden
 */
function setLoggedIn($username, $password, $remember){
	global $dbc;
	$encrypted = crypt(md5($password),md5($username));
	
	$query = 'SELECT username, password, firstname, type FROM user WHERE username = \''. $username .'\' AND password = \''. $encrypted .'\';';
	$result = mysqli_query($dbc, $query);
	if($result->num_rows == 1){
		$userrow = mysqli_fetch_object($result);
			
		if($remember){
			setcookie('user', $userrow->username, time()+(7 * 24 * 60 * 60), '/myaccount', BASE_URL);
			setcookie('password', $userrow->password, time()+(7 * 24 * 60 * 60), '/myaccount', BASE_URL);
		}
		else{
			setcookie('user', $userrow->username, false, '/myaccount', BASE_URL);
			setcookie('password', $userrow->password, false, '/myaccount', BASE_URL);
		}
		session_start();
		
		$_SESSION['logged_in'] = true;
		$_SESSION['user'] = $userrow->username;
		$_SESSION['firstname'] = $userrow->firstname;
		$_SESSION['user_type'] = $userrow->type;
		
		return true;
	}
	else {
		session_start();
		
		$_SESSION['loginerror'] = 'There was an error logging you into the system';
		return false;
	}
	
}


?>