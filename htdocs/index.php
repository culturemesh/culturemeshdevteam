<?php
	include_once("data/dal_user.php");
	
	ini_set('display_errors', true);
	error_reporting(E_ALL ^ E_NOTICE);
	include "log.php";
	
	session_name("myDiaspora");
	session_start();
	
	if (isset($_SESSION['uid']))
		$user_email = User::getMemberEmail($_SESSION['uid']);
	else
		$user_email = "";
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
		
		<?php
		/////////////////////////////////////////////////////////////////////
		// MAKING SURE PICTURES VARY N SUCH
		
		$bg_links = array("images/cmfrontpage_image1.png", 
			"images/cmfrontpage_image2.png", 
			"images/cmfrontpage_image3.png");
		
		$i = rand(0,2);
		
		if (isset($_SESSION['cur_bg']))
		{
			if ($_SESSION['cur_bg'] == $i)
			{
				$i+=1;
				if ($i > 2)
				{
					$i = 0;
					$_SESSION['cur_bg'] = $i;
				}
				else
					$_SESSION['cur_bg'] = $i;
			}
			else
				$_SESSION['cur_bg'] = $i;
		}
		else
			$_SESSION['cur_bg'] = $i;
		?>
		
		<?php // NOTE THE PHP IN THE BACKGROUND STMT BELOW!!! 
		?>
		<style type='text/css'>
		#stage-area
		{
			background:url(<?php echo $bg_links[$i]; ?>);
		}
		
		<?php if (isset($_SESSION['uid'])) : ?>
			#login-link {
			    display:none;
			}
			
			#register-link {
			    display:none;
			}
		<?php else : ?>
			#welcome {
			    display: none;
			}
			
			#sign-out {
			    display: none;
			}
			
		<?php endif; ?>
		
		</style>
	</head>
	<body id="index">
		<div class="wrapper">
			<?php
				include "index-header.php";
			?>
			<div id="stage-area">
				<div id="stage-content">
					<h3 id="stage-title">Connecting the world's diasporas</h3>
					<div id="search-bar">
						<form id="search-form" action="">
							<input type="text" class="stage-input" id="search-1" value="Find people who"></input>
							<input type="text" class="stage-input" id="search-2" value="Near"></input>
							<input type="submit" class="stage-button" value="Search"></input>
						</form>
					</div>
				</div>
			</div>
			<div id="bottom-section">
				<div id="vision" class="bottom-div">
					<h4>Our Vision</h4>
					<p class="bottom-text"> Millions of people live, work, and
					play outside of their home towns, provinces,
					states, and countries.
					</p>
					<p class="bottom-text"> At CultureMesh, we're building networks to
					match these real-world dynamics and knit the
					diverse fabrics of our world together.
					</p>
				</div>
				<div id="process" class="bottom-div">
					<h4>How it works</h4>
					<ol>
						<li><span>1</span>
						    <p class="bottom-text">Join a network you belong to.
						    Many places feel like home? At CultureMesh
						    you can easily switch between networks.
						    </p>
						</li>
						<li><span>2</span>
						    <p class="bottom-text">Join the conversation. Post your
						    thoughts and opinions. Share what's
						    new!
						    </p>
						</li>
						<li><span>3</span>
						    <p class="bottom-text">Connect to your diaspora - the world
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