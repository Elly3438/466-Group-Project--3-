<?php 
require('./includes/common.php');
require('./includes/helpers.inc.php');
require('./includes/begin.php');
?>


<?php include("includes/header.php"); ?>
<div class="featured-content">
		<div class="featured-buttons"><ul><li class="button-active">1</li><li>2</li><li>3</li><li>4</li></ul></div>
		<div class="featured-slides">
		<div class="featured-slide slide-active"><img src="./images/featured_pic_1.jpg" />
			<div class="featured-slide-content"><a href="<?php echo BASE_URL; ?>/search/index.php?p4=Violin">Shop Violins</a>
			<p>Our wide selection of Violins will surely wow you.</p></div></div>
		<div class="featured-slide"><img src="./images/featured_pic_2.jpg" />
			<div class="featured-slide-content"><a href="<?php echo BASE_URL; ?>/search/index.php?p4=Violin">Shop Violas</a>
			<p>Need a Viola? Shop with us!</p></div></div>
		<div class="featured-slide"><img src="./images/featured_pic_3.jpg" />
			<div class="featured-slide-content"><a href="<?php echo BASE_URL; ?>/search/index.php?p4=Violin">Shop Cellos</a>
			<p>We offer a wide variety of different Cellos.</p></div></div>
		<div class="featured-slide"><img src="./images/featured_pic_4.jpg" />
			<div class="featured-slide-content"><a href="<?php echo BASE_URL; ?>/search/index.php?p4=Violin">Shop Basses</a>
			<p>Shop our glorious selection of Basses!</p></div></div>
	</div></div>

<?php include("includes/footer.php"); ?>

