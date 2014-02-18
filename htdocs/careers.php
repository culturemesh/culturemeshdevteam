<?php
require_once 'log.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php
	include 'headinclude.php';
	?>
	<title>Careers - <?php echo DOMAIN_NAME; ?></title>
	<meta name="keywords" content="" />
	<meta name="description" content="Get your start-up up & running with Kos to Coast Development! If your startup is understaffed, we'll step in and give you a hand with software development." />
        
        <script src="http://www.google.com/jsapi" type="text/javascript"></script>
        <script type="text/javascript">google.load("jquery","1.6.1");</script>
        <link rel="stylesheet" href="css/jsquares.css" type="text/css" media="all" />
        <script src="js/jquery.hoverintent.min.js" type="text/javascript"></script>
	<script src="js/jquery.jsquares.js" type="text/javascript"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$('#js-container').jsquares();
		});
	</script>
</head>
<body id="careers" class="info-pg">
     <?php
	$boxes = array(
		// sizes are styled through css and top, left css attributes are hard-coded on the div
		array('size' => 3, 'top' => 0, 'left' => 0),
		array('size' => 2, 'top' => 0,'left' => 224),
		array('size' => 2, 'top' => 78, 'left' => 224),
		array('size' => 3, 'top' => 0, 'left' => 336),
		array('size' => 2, 'top' => 0, 'left' => 560),
		array('size' => 2, 'top' => 78, 'left' => 560),
		array('size' => 2, 'top' => 156, 'left' => 0),
		array('size' => 3, 'top' => 156, 'left' => 112),
		array('size' => 2, 'top' => 156, 'left' => 336),
		array('size' => 1, 'top' => 156, 'left' => 448),
		array('size' => 1, 'top' => 195, 'left' => 448),
		array('size' => 2, 'top' => 156, 'left' => 504),
		array('size' => 1, 'top' => 156, 'left' => 616),
		array('size' => 1, 'top' => 234, 'left' => 56),
		array('size' => 1, 'top' => 234, 'left' => 336)
	);
	?>
    <div class="wrapper">
        <?php
        include 'header.php';
        ?>
        <h2 class="pheader text-center">Careers</h2>
        <p class="text-center">CultureMesh is building networks to match the real-world dynamics of people living, working, and traveling outside of their home countries to knit the diverse fabrics of our world together.</p>
        <a href="" class="btn cm-button center-elem btn-big">See Current Openings</a>
        
        <div id="js-container">
		<?php
		for($i=0;$i<=count($boxes);$i++){
                    $box = $boxes[$i];
		?>
			<div class="js-image size-<?php echo $box['size']; ?>" style="top:<?php echo $box['top']; ?>px;left:<?php echo $box['left']; ?>px;">		
				<a href="#wolf!"><img class="js-small-image" src="images/wolf-moon.jpg"/></a>
				<div class="js-small-caption">
					<span>We're friendly.</span>
				</div>
				<div class="js-overlay-caption-content">
					<h4>Blah image title</h4>
					<p>
						Random stuff that'll match each picture...blah
					</p>
				</div>
			</div>
		
		<?php } ?>

	</div>
        
        <div class="mid-grid">
            
        </div>
        
        <div class="bottom-info">
            <h2 class="pheader text-center">Current Openings</h2>
            <ul class="three-section-pics">
                <li><h3 class="pheader">Engineering</h3>
                    <a href="" class="btn cm-button">See Openings</a>
                </li>
                <li><h3 class="pheader">Creative</h3>
                    <a href="" class="btn cm-button">See Openings</a>
                </li>
                <li><h3 class="pheader">Business</h3>
                    <a href="" class="btn cm-button">See Openings</a>
                </li>
            </ul>
        </div>
    </div>
</body>
<?php
include 'footer.php';
?>
</html>