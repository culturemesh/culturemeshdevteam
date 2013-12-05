<?php
	ini_set('display_errors', true);
	error_reporting(E_ALL ^ E_NOTICE);
	include "log.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<?php
			include "headinclude.php";
		?>
		
		<title>About - <?php echo DOMAIN_NAME; ?></title>
		<meta name="keywords" content="" />
		<meta name="description" content="Welcome to CultureMesh - Connecting the world's diasporas!" />
	</head>
	<body id="index">
		<div class="wrapper">
			<?php
				include "header.php";
			?>
			<?php
				include "footer.php";
			?>
		</div>
	</body>
</html>