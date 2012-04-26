<?php
	session_start();

// If the session vars aren't set, try to set them with a cookie
if (!isset($_SESSION['username'])) {
	if (isset($_COOKIE[md5(COOKIE_NAME)])) {
	    $auth_token = $_COOKIE[md5(COOKIE_NAME)];
	
	    //fetch user info based on auth_token
	    $query = 'SELECT username, firstname '.
	     	     'FROM user '.
	      	     'WHERE auth_token = \''. $auth_token .'\'';
	    $result = mysqli_query($dbc, $query);
	    if(!$result){
	    	$msg = 'Error connecting to db '. mysqli_errno($dbc);
		}
		else{
	  		$row = mysqli_fetch_object($result);
	     	$_SESSION['firstname'] = $row->firstname;
	     	$_SESSION['username'] = $row->username;      	
	    }
	}
}
?>