<?php
/** PHP functions go in here **/

/** Checks to see if email is already in use for user registration
 *  Arguments: value to check for in the database, and which column it is in
 * Author: Jeffrey Bowden
 */
function form_validate_unique($value, $option){
	$query = 'SELECT * FROM user WHERE '. $option .' = \''. mysql_real_escape_string($value) .'\';';
	$result = db_query($query);
	return(mysqli_num_rows($result) == 0);
}

/** Peforms a query on the database
 *  Arguments: a query string
 * Author: Jeffrey Bowden
 */
function db_query($query){
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
	  or die('Error connecting to the Server');
	  
	$result = mysqli_query($dbc, $query)
        or die('Error querying DB');
        
    mysqli_close($dbc);
    
    return $result;
        
}

/** Adds a User to the database
 *  Arguments: An array of input values
 * Author: Jeffrey Bowden
 */
function addUser($arr){
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
	
	$result = db_query($query);
	if(!$result){
		$msg = 'Error - not added to database'. mysql_errno() .'<br />';
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
	$encrypted = crypt(md5($password),md5($username));
	
	$query = 'SELECT username, password, firstname, type FROM user WHERE username = \''. $username .'\' AND password = \''. $encrypted .'\';';
	$result = db_query($query);
	if(mysqli_num_rows($result) == 1){
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