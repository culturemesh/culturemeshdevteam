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
				<div id="stage-content">
					<h3 id="stage-title">Connecting the World's Diasporas</h3>
					<div id="search-bar">
						<div id="search-floater">
						<div id="search-content">
						<form id="search-form">
							<input type="text" class="stage-input" value="Find People who"></input>
							<input type="text" class="stage-input" value="Near"></input>
							<input type="submit" class="stage-button" value="Search"></input>
						</form>
						</div>
						</div>
					</div>
				</div>
			</div>
			<div id="bottom-section">
				<div id="vision" class="bottom-div">
					<h4>Our Vision</h4>
					<p> Millions of people live, work, and
					play outside of their home towns, provinces,
					states, and countries.
					</p>
					<p> At CultureMesh, we're building networks to
					match these real-world dynamics and knit the
					diverse fabrics of our world together.
					</p>
				</div>
				<div id="process" class="bottom-div">
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
				<div id="pop-networks" class="bottom-div">
					<h4>Popular Networks</h4>
				</div>
				<div class="clear"></div>
			</div>
			<?php
				include "footer.php";
			?>
		</div>
	</body>
</html>