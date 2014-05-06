<?php
	//ini_set('display_errors', true);
	//error_reporting(E_ALL ^ E_NOTICE);
	include "log.php";
	include_once "zz341/fxn.php";
	include_once "data/dal_network.php";
	include_once "data/dal_network_registration.php";
	include_once "data/dal_user.php";
	include_once "html_builder.php";
	
	//session_name("myDiaspora");
	//session_start();

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
////////////////////////////////////////////////////
	$con = getDBConnection();
	
	$guest = true;
	$member = false;
	
	if (!isset($_SESSION['uid']))
		$guest = true;
	else
	{
		$guest = false;
		$netreg = new NetworkRegistrationDT();
		$netreg->id_user = $user->id;
		$netreg->id_network = $_GET['id'];
		$member = NetworkRegistration::checkRegistration($netreg);
	}
	
	$id = $con->real_escape_string($_GET['id']);
	$network = Network::getNetworkById($id, $con);
	$network->member_count = NetworkRegistration::getMemberCount($id, $con);
	$network->post_count = Post::getPostCount($id, $con);
	$_SESSION['cur_network'] = $network->id;
	
	$events = Event::getEventsByNetworkId($id, $con);
	$posts = Post::getPostsByNetworkId($id, $con);
	
	// make an event calendar
	$months = array("January", "February", "March", "April",
		"May", "June", "July", "August", "September",
		"October", "November", "December");

	$calendar = array();
	$years = array();

	foreach($events as $event)
	{
		// add year to array as keys
		//   -- get year
		$dt = new DateTime($event->event_date);
		$year = $dt->format('y');

		// 
		if (!isset($calendar[$year])) {
			$calendar[$year] = array();
			array_push($years, $year);

			// add months to array as keys
			foreach($months as $month)
			{
				$calendar[$year][$month] = array();
			}
		}


		// get month of event
		$month = $dt->format('n');

		// push event into array
		array_unshift( $calendar[$year][$months[$month - 1]], $event);
	}

	//var_dump($calendar);

	//var_dump($posts);
	
	mysqli_close($con);
/////////////////////////////////////////////////////
?>
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
		
		</style>
		
		 <script>
		/*
		$(function() {
		$( "#datetimepicker" ).datetimepicker();
		});
		 */
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
						<?php HTMLBuilder::googleMapsEmbed(); ?>
<!--
						<div class="map">
							<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d202
							359.87608164057!2d-122.32141071468104!3d37.581606634196056!2m3!1f0!2f0!
							3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808fcae48af93ff5%3A0xb99d8c0aca9f717b
							!2sSan+Jose%2C+CA!5e0!3m2!1sen!2sus!4v1389926877454" width="100%" height="252"
							frameborder="0" style="border:0"></iframe>
						</div>
-->
						<div class="tags"></div>
						<div class="suggestions">
							<h4 class="h-network">People who viewed this network also viewed:</h4>
						</div>
					</div>
				</div>
				<div class="net-right">
					<?php HTMLBuilder::displaySearchBar(); ?>	
					<?php //HTMLBuilder::displayLrgNetwork($network); ?>
					<div>
						<div class='net-info'>
							<h1 class='h-network'><?php echo HTMLBuilder::formatNetworkTitle($network); ?></h1>
							<div class="reg-guest">
								<form method="POST" action="network_join.php">
									<p class='lrg-network-stats'><?php echo $network->member_count; ?> Members | <?php echo $network->post_count; ?> Posts</p>
									<button class="network">Join us!</button>
								</form>
							</div>
							<div class="guest">
								<p class='lrg-network-stats'><?php echo $network->member_count; ?> Members | <?php echo $network->post_count; ?> Posts</p>
								<button class="network" onclick="$('#register_modal').modal('show');">Join us!</button>
							</div>
							<div class="member">
								<p class='lrg-network-stats'><?php echo $network->member_count; ?> Members | <?php echo $network->post_count; ?> Posts</p>
							</div>
						</div>
						<div class="clear"></div>
					</div>
					</br>
					<hr width="700">
					<div id="event-wall">
						<h2 class="h-network">Upcoming Events</h2>
						<!--<ul class="network">-->
						<div>
						<button id="slider-left" class="slider-button"></button>
						<div id="slider-content" class="event-slider">
						<table id="slider-table" class="network event">
							<thead></thead>
							<tbody>
							<tr>
								<?php 
								foreach($years as $year) {
									foreach($months as $month)
									{
										if (empty($calendar[$year][$month]))
											continue;

										HTMLBuilder::displayEventMonth($month, $year);

										// cycle through the month's events
										for ($i = 0; $i < count($calendar[$year][$month]); $i++)
										{
											HTMLBuilder::displayEventCard($calendar[$year][$month][$i]);
											HTMLBuilder::displayEventModal($calendar[$year][$month][$i]);
										} 
									}
								}
								?>
							</tr>
							</tbody>
							</tr>
						</table>
						</div>
						<button id="slider-right" class="slider-button"></button>
						</div>
						<div class="clear"></div>
						<!--</ul>-->
					</div>
					<button id="event-post" class="network member" onclick="toggleEventForm()">Post Event</button>
					</br>
					</br>
					<div id="event-maker">
						<form class="event-form" method="POST" action="network_post-event.php" enctype="multipart/form-data">
							<div>
							<input type="text" id="title" name="title" class="event-text" placeholder="Name of Event "></input></br>
							<input type="text" id="datetimepicker1" name="datetime" class="event-text datetimepicker" placeholder="Event Date">
							<input type="text" class="hidden-field" name="date"></input>
							<input type="text" name="address_1" class="event-text" placeholder="Address 1"/>
							<input type="text" name="address_2" class="event-text" placeholder="Address 2"/>
							<textarea id="description" name="description" class="event-text" placeholder="What's happening?"></textarea>
							<div id="clear"></div>
							<input type="text" class="hidden-field" name="city" value=<?php echo $network->city_cur;?>/></input>
							<input type="text" class="hidden-field" name="country" value=<?php echo $network->country_cur;?>/></input>
							<input type="submit" class="network" value="Post"></input>
							</div>
						</form>
					</div>
					</br>
					<hr width="700">
					<div id="post-wall">
						<h2 class="h-network">Posts</h2>
						<form method="POST" class="member" action="network_post.php">
							<img id="profile-post" src="images/blank_profile.png" width="45" height="45">
							<textarea class="post-text" name="post_text" placeholder="Post something..."></textarea>
							<div class="clear"></div>
							<input type="submit" class="network" value="Send"></input>
							<input type="hidden" name="post_class" value="o"></input>
							<input type="hidden" name="post_original" value="NULL"></input>
						</form>
						<ul id="post-wall-ul" class="network">
						<?php 
						foreach($posts as $post)
							HTMLBuilder::displayPost($post); 
						?>
						</ul>
						<!--<script src="js/post-wall.js"></script>-->
						<script>
						/*
						var wall = document.getElementById("post-wall-ul");
							var postData;
							var grabData = function(data) {
								postData = data;
								replyPosts = [];
								for (var i = 0; i < postData.length; i++) {
									if (postData[i]['post_class'] == 'o')
									  { wall.appendChild(createParent(postData, i)); }
								/*
									else 
									{ 
										origId = postData[i]['post_original'];
										if (replyPosts[origId] == undefined) {
											replyPosts[origId] = [];
											replyPosts[origId].push(postData[i];
										}
										else
										  { replyPosts[origId].push(postData); }
								       	}
								 *//*
								}
								// do something with replyPostslength data
								// add a div for reply
							}

							loadPostData(<?php echo $_SESSION['cur_network']; ?>, grabData);
						 */
						</script>
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
	<script src="js/searchbar.js"></script>
	<script src="js/slider.js"></script>
	<script src="js/event-wall.js"></script>
	<script>
		$(".datetimepicker").datetimepicker();
	</script>	
	</script>
</html>
