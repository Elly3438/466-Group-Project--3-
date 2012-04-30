<?php
require('../includes/common.php');
require('../includes/helpers.inc.php');
include('../includes/searchhelper.php');
require('../includes/begin.php');

if(isset($_SESSION['register'])){
	echo 'Registration Complete<br />';
	echo 'Thanks for registering, '. $_SESSION['firstname'] .'...<br />';
	echo 'Checking for cookie<br />';
	print_r($_COOKIE);
}

include('../includes/header.php');
?>
	
	
	<div class="about-page-content" style="width:900px;height:640px; background-color:#DDD; margin:0 auto;">
	<div class = "photo-about" style="padding:10px; margin:10px; float:left; background-color:#222;">
				<img src="../images/owner.jpg" width="300"><br><p style="color:white; align:center;">Arnold Kimball - Store Owner</p>

	</div>
	<div class="info-about" style="padding:10px; margin-left:350px; position:relative;top:10px; width:520px; height:600px; background-color:#f0f0f0;"> 
		<h1>Kimball Music</h1></h1><p>We are a family owned and operated business that sells and leases various string instruments for 31 years.  Many of our customesrs found 
			that both our services and instruments we offer is the best out there.   Our lineup of violn,
			viola, cello and bass, are one of the finest in the country. Also we offer only name brand accessories which we stand by. <br><br>  <strong>For more infomation contact us at:</strong><br>Email: info@kimballmusic.com<br> 
			Phone:1-555-555-5555 </p>
		</div>
	</div>
	
	<?php
include('../includes/footer.php');

?>