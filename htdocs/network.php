<?php
	ini_set('display_errors', true);
	error_reporting(E_ALL ^ E_NOTICE);
	include "log.php";
	include_once "zz341/fxn.php";
	include_once "data/dal_network.php";
	include_once "data/dal_network_registration.php";
	include_once "data/dal_user.php";
	include_once "html_builder.php";
	
	session_name("myDiaspora");
	session_start();

	$con = getDBConnection();
	
	$guest = true;
	$member = false;
	
	if (!isset($_SESSION['uid']))
		$guest = true;
	else
	{
		$guest = false;
		$user = User::getUserById($_SESSION['uid'], $con);
		$user_email = $user->email;
		
		$netreg = new NetworkRegistrationDT();
		$netreg->id_user = $user->id;
		$netreg->id_network = $_GET['id'];
		$member = NetworkRegistration::checkRegistration($netreg);
	}
	
	$id = mysql_escape_string($_GET['id']);
	$network = Network::getNetworkById($id, $con);
	$network->member_count = NetworkRegistration::getMemberCount($id, $con);
	$network->post_count = Post::getPostCount($id, $con);
	$_SESSION['cur_network'] = $network->id;
	
	$events = Event::getEventsByNetworkId($id, $con);
	$posts = Post::getPostsByNetworkId($id, $con);
	
	//var_dump($posts);
	
	mysqli_close($con);
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
		<style type='text/css'>
		
		<?php 	// NOT A MEMBER OF NETWORK
			if (!$member) : ?>
		.member {
			display:none;
		}
		<?php endif; ?>
		
		<?php 	// REGISTERED OR A MEMBER OF SITE
			if (!$guest) : ?>
		.guest {
			display:none;
		}
		<?php endif; ?>
		
		<?php 	// A REGISTERED GUEST OF SITE
			if ($member || $guest) : ?>
		.reg-guest {
			display:none;
		}
		<?php endif; ?>
		
		<?php if (isset($_SESSION['uid'])) : ?>
			#login-link {
			    display:none;
			}
			
			#register-link {
			    display:none;
			}
			
			.guest {
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
		
		 <script>
		$(function() {
		$( "#datetimepicker" ).datetimepicker();
		});
		
		function toggleEventForm(){
			if (document.getElementById("event-maker")) {
				var elem = document.getElementById("event-maker");
				if (elem.style.display == "none" || elem.style.display == "")
				{
					elem.style.display = "block";
				}
				else if (elem.style.display == "block")
					elem.style.display = "none";
			}
		}
		</script>
		
	</head>
	<body>
		<div class="wrapper">
			<?php
				include "header.php";
			?>
			<div content>
				<div class="net-left">
					<div class="leftbar">
						<div class="map">
							<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d202
							359.87608164057!2d-122.32141071468104!3d37.581606634196056!2m3!1f0!2f0!
							3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808fcae48af93ff5%3A0xb99d8c0aca9f717b
							!2sSan+Jose%2C+CA!5e0!3m2!1sen!2sus!4v1389926877454" width="100%" height="252"
							frameborder="0" style="border:0"></iframe>
						</div>
						<div class="tags"></div>
						<div class="suggestions">
							<h4 class="h-network">People who viewed this network also viewed:</h4>
						</div>
					</div>
				</div>
				<div class="net-right">
					<div id="search">
						<form method="GET" action="search_results.php">
							<input type="text" class="net-search" name="search-1" value="Find people who "/>
							<input type="text" class="net-search" name="search-2" value="near"/>
							<input type="submit" style="display:none"/>
						</form>
					</div>
					<?php HTMLBuilder::displayLrgNetwork($network); ?>
					<div class="reg-guest">
						<form method="POST" action="network_join.php">
							<button class="network">Join this Network!</button>
						</form>
					</div>
					<div class="guest">
						<button class="network" onclick="$('#register_modal').modal('show');">Join this Network!</button>
					</div>
					<div id="event-wall">
						<h2 class="h-network">Upcoming Events</h2>
						<ul class="network">
						<?php foreach($events as $event)
							HTMLBuilder::displayEvent($event); 
						?>
						</ul>
					</div>
					<a class="network member" onclick="toggleEventForm()">Post Event</a>
					<div id="event-maker">
						<form class="event-form" method="POST" action="network_post-event.php" enctype="multipart/form-data">
							<div>
							<input type="text" id="title" name="title" class="event-text" placeholder="Name of Event "></input></br>
							<input type="text" id="datetimepicker" name="datetime" class="event-text" placeholder="Event Date">
							<input type="text" class="hidden-field" name="date"></input>
							<input type="text" name="address_1" class="event-text" placeholder="Address 1"/>
							<input type="text" name="address_2" class="event-text" placeholder="Address 2"/>
							<textarea id="description" name="description" class="event-text" placeholder="What's happening?"></textarea>
							<div id="clear"></div>
							<input type="text" class="hidden-field" name="img_file"></input>
							<input type="text" class="hidden-field" name="vid_file"></input>
							<input type="file"  size="60"/>
							<input type="file"  size="60"/>
							<input type="text" class="hidden-field" name="city" value=<?php echo $network->city_cur;?>/></input>
							<input type="text" class="hidden-field" name="region" value=<?php echo $network->region_cur;?>/></input>
							<input type="submit" class="network" value="Post"></input>
							</div>
						</form>
					</div>
					<div id="post-wall">
						<h2 class="h-network">Posts</h2>
						<form method="POST" class="member" action="network_post.php">
							<img id="profile-post" src="images/blank_profile.png" width="45" height="45">
							<textarea class="post-text" name="post_text" placeholder="Post something..."></textarea>
							<div class="clear"></div>
							<input type="submit" class="network" value="Send"></input>
						</form>
						<ul class="network">
						<?php 
						foreach($posts as $post)
							HTMLBuilder::displayPost($post); 
						?>
						</ul>
					</div>
				</div>
				<div class="clear"></div>
			</div>
			<?php
				include "footer.php";
			?>
		</div>
	</body>
	<link rel="stylesheet" type="text/css" href="js/jsdatetime/jquery.datetimepicker.css"/ >
	<script src="js/jsdatetime/jquery.datetimepicker.js"></script>
</html>
