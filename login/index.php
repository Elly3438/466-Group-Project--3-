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
	
}
if (isset($_SESSION['username'])){
	header('Location: '.BASE_URL.'/myaccount/');
}

include('../includes/header.php');
?>

<div id="login">
	<?php
	if(!empty($login_errors)){
			foreach($login_errors as $login_error => $val){
					echo $val;
			}
	}
	?>
	<form action="./index.php" method="post" accept-charset="utf-8">
		<h1>LOG IN</h1>
		<div class="form-item" <?php echo (isset($login_errors['username']) ? 'style="color: #ff0000;"' : ''); ?>>
			<label for="username">Username:</label>
			<input type="text" maxlength="20" name="username" id="username" 
				value="<?php echo (isset($clean['username']) ? $clean['username'] : ''); ?>" >
		</div>
		<div class="form-item" <?php echo (isset($login_errors['password']) ? 'style="color: #ff0000;"' : ''); ?>>
			<label for="password">Password:</label>
			<input type="password" maxlength="20" name="password" id="password" >
		</div>
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