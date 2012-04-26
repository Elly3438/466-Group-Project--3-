<?php /************************************
--Login Page--
Author: Jeffrey Bowden
*****************************************/
require('../includes/common.php');
require('../includes/helpers.inc.php');
require('../includes/begin.php');

$login_errors = array();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	
	/** Initial POST cleaning **/
	$clean = array();
	foreach($_POST as $key => $value){
		$value = strip_tags(trim($value)); //strip out script tags and extra space
		$value = preg_replace('/"/', '', $value); //replace any " that may break the code
		$clean[$key] = $value;
	}

	$login_errors = processLogin($clean, $login_errors);
	
	if(!empty($login_errors)){
		foreach($login_errors as $login_error => $val){
				echo $val;
		}
	}
	else {
		echo 'You are logged in '. $_SESSION['firstname'];
	}
}

include('../includes/header.php');
?>

<div id="login">
	<form action="./index.php" method="post" accept-charset="utf-8">
		<h1>LOG IN</h1>
			<?php echo (isset($login_errors['login-error']) ? '<div class="login-error">'. $login_errors['login-error'] .'</div>' : ''); ?>
		<div class="form-item" <?php echo (isset($login_errors['username']) ? 'style="color: #ff0000;"' : ''); ?>>
			<label for="username">Username:</label>
			<input type="text" maxlength="20" name="username" id="username" 
				value="<?php echo (isset($clean['username']) ? $clean['username'] : ''); ?>" >
		</div>
		<?php echo (isset($login_errors['username']) ? '<div class="login-error">'. $login_errors['username'] .'</div>' : ''); ?>
		<div class="form-item" <?php echo (isset($login_errors['password']) ? 'style="color: #ff0000;"' : ''); ?>>
			<label for="password">Password:</label>
			<input type="password" maxlength="20" name="password" id="password" >
		</div>
		<?php echo (isset($login_errors['password']) ? '<div class="login-error">'. $login_errors['password'] .'</div>' : ''); ?>
		<div class="form-item">
			<label for="remember">&nbsp;</label>
			<input type="checkbox" name="remember" id="remember" value="yes" >
			Remember me?
		</div>
		<div class="form-item">
			<button type="submit" name="user-login" value="1">Log In</button>
		</div>
		
		<h2>Not a member? <a href="../register">Sign up!</a></h2>
	</form>
</div>
<?php
	include('../includes/footer.php');
?>