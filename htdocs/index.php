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
		
		<title>CultureMesh - Connecting the World's Diasporas </title>
		<meta name="keywords" content="" />
		<meta name="description" content="Welcome to CultureMesh - Connecting the world's diasporas!" />
	</head>
	<body id="index">
		<div class="wrapper">
			<?php
				include "header.php";
			?>
			<div id="stage-area">
				<p>Connecting the World's Diasporas</p>
				<p> Search bar n things </p>
			</div>
			<div id="bottom-section">
				<div id="vision">
					<h4>Our Vision</h4>
				</div>
				<div id="process">
					<h4>How it works</h4>
					<ol>
						<li>
						    <p>Join a network you belong to.
						    Many places feel like home? At CultureMesh
						    you can easily switch between networks.
						    </p>
						</li>
						<li>
						    <p>Join the conversation. Post your
						    thoughts and opinions. Share what's
						    new!
						    </p>
						</li>
						<li>
						    <p>Connect to your diaspora - the world
						    is your playground!
						    </p>
						</li>
					</ol>
				</div>
				<div id="pop-networks">
					<h4>Popular Networks</h4>
				</div>
			</div>
			<?php
				include "footer.php";
			?>
		</div>
	</body>
</html>