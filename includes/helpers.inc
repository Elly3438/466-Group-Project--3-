<?php
/** PHP functions go in here **/
print_r($errors);
	/** first name processing **/
	if(!empty($clean['firstname'])){
		if(preg_match('/^[A-Z \'.-]+$/i', $clean['firstname'])){
			$form_inputs['firstname'] = mysql_real_escape_string($clean['firstname']);		
		} 
		else {
			$reg_errors['fname'] = 'First Name needs to be valid characters<br />';
		}
	}
	else {
		$reg_errors['fname'] = 'You need to enter a First Name.<br />';
	}
	/** last name processing **/
	if(!empty($_POST['lastname'])){
		if(preg_match('/^[A-Z \'.-]+$/i', trim($_POST['lastname']))){
			$form_inputs['lastname'] = mysql_real_escape_string(trim($_POST['lastname']));		
		}
		else {
			$reg_errors['lname'] = 'Last Name needs to be valid characters<br />';
		}
	}
	else {
		$reg_errors['lname'] = 'You need to enter a Last Name.<br />';
	}
	/** street1 processing **/
	if(!empty($_POST['street1'])){
		if(preg_match('/^[A-Z0-9 \'.-]+$/i', trim($_POST['street1']))){
			$form_inputs['street1'] = mysql_real_escape_string(trim($_POST['street1']));		
		}
		else {
			$reg_errors['street1'] = 'Street1 needs to be valid characters<br />';
		}
	}
	else {
		$reg_errors['street1'] = 'You need to enter Street1.<br />';
	}
	/** street2 processing **/
	if(!empty($_POST['street2'])){
		if(preg_match('/^[A-Z0-9 \'.-]+$/i', trim($_POST['street2']))){
			$form_inputs['street2'] = mysql_real_escape_string(trim($_POST['street2']));		
		}
		else {
			$reg_errors['street2'] = 'Street2 needs to be valid characters<br />';
		}
	}
	else {
		$form_inputs['street2'] = '';
	}
	/** city processing **/
	if(!empty($_POST['city'])){
		if(preg_match('/^[A-Z \'.-]+$/i', trim($_POST['city']))){
			$form_inputs['city'] = mysql_real_escape_string(trim($_POST['city']));		
		}
		else {
			$reg_errors['city'] = 'City needs to be valid characters<br />';
		}
	}
	else {
		$reg_errors['city'] = 'You need to enter City.<br />';
	}
	/** zip code processing **/
	if(!empty($_POST['zip'])){
		if(preg_match('/^([0-9]{5})(-[0-9]{4})?$/i', trim($_POST['zip']))){
			$form_inputs['zip'] = mysql_real_escape_string(trim($_POST['zip']));		
		}
		else {
			$reg_errors['zip'] = 'Zip code needs to be 5 digits, or in the format 12345-1234<br />';
		}
	}
	else {
		$reg_errors['zip'] = 'You need to enter Zip.<br />';
	}
	/**state processing **/
	if($_POST['state'] != ''){
		$form_inputs['state'] = mysql_real_escape_string($_POST['state']);		
	}
	else {
		$reg_errors['state'] = 'You need to select a State.<br />';
	}
	
	/**phone number processing - US phone numbers only **/
	if(!empty($_POST['phone'])){
		$phone = $_POST['phone'];
		$phone = preg_replace('/\D/', '', $phone);
		if(strlen($phone) == 11 && substr($phone, 0, 1) == 1){
			$phone = substr($phone, 1);
		}
		if(preg_match('/^([0-9]{10})$/i', $phone)){
			$form_inputs['phone'] = mysql_real_escape_string($phone);	
		}
		elseif(preg_match('/^([0-9]{7})$/i', $phone)){
			$reg_errors['phone'] = 'You need to enter an area code for your Phone Number<br />';
		}
		else {
			$reg_errors['phone'] = 'Phone Number needs to be valid characters and format 888-888-8888<br />';
		}
	}
	else {
		$phone = '';
		$reg_errors['phone'] = 'You need to enter a Phone Number.<br />';
	}
	/** Email processing **/
	if(!empty($_POST['email'])){
		if (filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL)) {
			if(form_validate_unique(trim($_POST['email']), 'email')){
				$form_inputs['email'] = mysql_real_escape_string(trim($_POST['email']));
			}
			else {
				$reg_errors['email'] = 'Email already in use.<br />';
			}
		}
		else {
			$reg_errors['email'] = 'Please enter a valid email address.<br />';
		}
	} 
	else {
		$reg_errors['email'] = 'You need to enter an Email.<br />';
	}
	/** Username processing **/
	if(!empty($_POST['username'])){
		if (preg_match('/^[A-Z0-9]{4,20}$/i', trim($_POST['username']))) {
			if(form_validate_unique(trim($_POST['username']), 'username')){
				$form_inputs['username'] = mysql_real_escape_string(trim($_POST['username']));
			}
			else {
				$reg_errors['username'] = 'Username already in use.<br />';
			}
		}
		else {
			$reg_errors['username'] = 'Please enter a valid Username (4-20 characters long).<br />';
		}
	} 
	else {
		$reg_errors['username'] = 'You need to enter a Username.<br />';
	}
		/** Password processing **/
	if(!empty($_POST['password'])){
		if (preg_match("/^(\w*(?=\w*\d)(?=\w*[a-z])(?=\w*[A-Z])\w*){6,20}$/", trim($_POST['password']))) {
			if(!empty($_POST['password1'])){
				if(trim($_POST['password']) == trim($_POST['password1'])){
					$form_inputs['password'] = mysql_real_escape_string(trim($_POST['password']));
				}
				else {
					$reg_errors['password1'] = 'Passwords do not match.<br />';
				}
			}
			else {
				$reg_errors['password1'] = 'You need to verify your Password.<br />';
			}
		}
		else {
			$reg_errors['password'] = 'Your Password is not valid (Must be 6-20 characters long, contain at least one caps character and one number).<br />';
		}
	} 
	else {
		$reg_errors['password'] = 'You need to enter a Password.<br />';
	}
?>