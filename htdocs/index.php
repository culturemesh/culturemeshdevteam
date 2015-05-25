<?php

	include_once("data/dal_user.php");
	
	include "log.php";
	
	include_once("data/dal_network.php");
	include_once("html_builder.php");
	include_once("environment.php");
	
	session_name("myDiaspora");
	session_start();
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
		
	//"images/cmfrontpage_image1.jpg",
	// too much white
		$bg_links = array( 
			"images/cmfrontpage_image1.jpg",
			"images/cmfrontpage_image2.jpg", 
			"images/cmfrontpage_image3.jpg",
			"images/cmfrontpage_image4.jpg",
			//"images/cmfrontpage_image5.jpg",
			"images/cmfrontpage_image6.jpg",
			"images/cmfrontpage_image7.jpg",
			//"images/cmfrontpage_image8.jpg",
			//"images/cmfrontpage_image9.jpg",
			//"images/cmfrontpage_image10.jpg",
			"images/cmfrontpage_image11.jpg",
			"images/cmfrontpage_image12.jpg",
			//"images/cmfrontpage_image13.jpg",
			//"images/cmfrontpage_image14.jpg",
			//"images/cmfrontpage_image15.jpg",
			"images/cmfrontpage_image16.jpg",
			"images/cmfrontpage_image17.jpg",
			//"images/cmfrontpage_image18.jpg",
			"images/cmfrontpage_image19.jpg",
			"images/cmfrontpage_image20.jpg",
			"images/cmfrontpage_image21.jpg",
			"images/cmfrontpage_image22.jpg",
			//"images/cmfrontpage_image23.jpg",
			"images/cmfrontpage_image24.jpg",
			"images/cmfrontpage_image25.jpg",
			"images/cmfrontpage_image26.jpg",
			"images/cmfrontpage_image27.jpg",
			"images/cmfrontpage_image28.jpg"
			);
		
		$i = rand(0,18);
		
		if (isset($_SESSION['cur_bg']))
		{
			if ($_SESSION['cur_bg'] == $i)
			{
				$i+=1;
				if ($i > 1)
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
				include "header.php";
			?>
			<?php if(isset($_GET['signout'])) : ?>
				<script>
					$("#signout_panel").show();
					$("#signout_panel").fadeOut(5000);
				</script>
			<?php endif; ?>
			<div id="stage-area">
				<div id="stage-content">
					<h3 id="stage-title">Connecting the world's diasporas</h3>
					<form id="search-form" class='stage' method="GET" action="search_results.php" autocomplete="off">
					<div id="opening" class='stage'>Find people who
					<select id="verb-select" name="verb" class="stage-input">
						<option value="arefrom">are from</option>
						<option value="speak">speak</option>
					</select>
					<span class='at stage'>In/Near</span>
					</div>
					<div id="search-bar">
							<input type="text" class="stage-input" name="search-1" id="search-1" autocomplete="off"></input>
								<ul id="s-query" class="search"></ul>
								<ul id="s-var" class="search"></ul>
								<input type="hidden" id="clik1" name="clik1" value=0></ul>
							<input type="text" class="stage-input" name="search-2" id="search-2" autocomplete="off"></input>
								<ul id="s-location" class="search"></ul>
								<input type="hidden" id="clik2" name="clik2" value=0></ul>
							<input type="submit" class="stage-button" value="SEARCH"></input>
							<input type="hidden" id="search-topic" name="search-topic"></input>
					</div>
					</form>
				</div>
			</div>
			<div id="bottom-section">
				<div id="vision" class="bottom-div">
					<h4>Our Vision</h4>
					<div class="bottom-div-material">
						<p class="bottom-text"> Millions of people live, work, and
						play outside of their home towns, provinces,
						states, and countries.
						</p>
						<p class="bottom-text"> At CultureMesh, we're building networks to
						match these real-world dynamics and knit the
						diverse fabrics of our world together.
						</p>
					</div>
				</div>
				<div id="process" class="bottom-div">
					<h4>How it works</h4>
					<div class="bottom-div-material">
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
				</div>
				<div id="pop-networks" class="bottom-div">
					<h4>Popular Networks</h4>
					<div class="bottom-div-material">
						<?php
						$networks = Network::getTopFourNetworks();
						
						for ($i = 0; $i < count($networks); $i++)
							HTMLBuilder::displayPopNetwork($networks[$i]);
						?>
					</div>
				</div>
				<div class="clear"></div>
			</div>
			<?php
				include "footer.php";
			?>
		</div>
	</body>
	<script src="js/searchbar.js"></script>
</html>
