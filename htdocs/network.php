<?php
//	ini_set('display_errors', true);
//	error_reporting(E_ALL ^ E_NOTICE);
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
	$pc_data = Post::getPostCountAA($id, $con);
	$network->post_count = $pc_data['total']; 
	$_SESSION['cur_network'] = $network->id;
	
	$events = Event::getEventsByNetworkId_D($id, $con);

	if (isset($_SESSION['uid']))
	{
		// check membership
		foreach($events as $event) {
			$result = EventRegistration::checkAttendance($_SESSION['uid'], $event->id, $con);
			if (mysqli_num_rows($result) > 0)
				$event->attending = true;
		}
	}

	$post_bounds = NULL;

	if (isset($_GET['ap'])) {
		$post_bounds = array(0, 9999);
	}
	else {
		$post_bounds = array(0, 10);
	}

	$posts = Post::getPostsByNetworkId($id, $post_bounds, $con);

	// create replies assoc array
	$replies = array();

	// for each post, check if it's in get
	// get replies from database,
	// push into array
	foreach ($posts as $post) {
		// get replies
		$prs = Post::getRepliesByParentId($post->id, $con);
		// push into array
		$replies[$post->id] = $prs;
	}

	// get replies
	/*
	if (isset($_GET['reply'])) {
		// create replies assoc array
		$replies = array();
		// examine GET array
		$pids = explode(' ', $_GET['reply']);

		// add keys into replies
		foreach ($pids as $pid) {
			$replies[$pid] = NULL;
		}

		// for each post, check if it's in get
		// get replies from database,
		// push into array
		foreach ($posts as $post) {
			if (in_array($post->id, $pids)) {
				$prs = Post::getRepliesByParentId($post->id, $con);
				// push into array
				$replies[$post->id] = $prs;
			}
		}
	}
	 */
	
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
		$year = $dt->format('Y');

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
		array_push( $calendar[$year][$months[$month - 1]], $event);
	}

	// Load events object into static page
	$json_events = json_encode($events);
	echo "<script>var events = {$json_events}</script>\n";

	//var_dump($calendar);
	//var_dump($posts);
	
	mysqli_close($con);

	// create location for gmap embed
	$location = '';
	if (isset($network->city_cur))
	   { $location .= urlencode($network->city_cur); }
	if (isset($network->region_cur))
	   { $location .= ','.urlencode($network->region_cur); }
	if (isset($network->country_cur))
	   { $location .= ','.urlencode($network->country_cur); }

	// check for image link
	$img_link = NULL;
	if($user->img_link == NULL)
		$img_link = 'images/blank_profile.png';
	else
		$img_link = IMG_DIR.$user->img_link;

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
					$("#event-post").text("Cancel");
				}
				else if (elem.style.display == "block") {
					elem.style.display = "none";
					$("#event-post").text("Post Event");
				}
			}
		}
		</script>
		
	</head>
	<body>
		<?php echo HTMLBuilder::displayShareScript(); ?>
		<div class="wrapper">


			<?php
				include "header.php";
			?>
			<div content>
				<div class="net-left">
					<div class="leftbar">
						<?php HTMLBuilder::googleMapsEmbed($location); ?>
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
								<form method="POST" action="network_leave.php">
									<button class="network">Leave Network</button>
									<input type="hidden" name="uid" value="<?php echo $_SESSION['uid']; ?>"/>
									<input type="hidden" name="nid" value="<?php echo $network->id; ?>"/>
								</form>
							</div>
							<div class="clear"></div>
							<?php echo HTMLBuilder::displayShare($network->id) ?>
							<div>
							<p class="network error" id="lnerror"></p>
							<p class="network error" id="jnerror"></p>
							</div>
						</div>
						<div class="clear"></div>
					</div>
					</br>
					<hr width="630">
					<!-------------- -->
					<ul class="nav nav-pills dashboard">
						<li id="post-nav" class="active"><a href="#post-wall" data-toggle="pill">Posts</a></li>
						<li id="event-nav"><a href="#event-wall" data-toggle="pill">Events</a></li>
					</ul>
					<!-------------- -->
					<div class="network tab-content">
					<div id="post-wall" class="tab-pane active">
						<form method="POST" class="member" action="network_post.php">
						<img id="profile-post" src="<?php echo $img_link; ?>" width="45" height="45">
							<textarea class="post-text" name="post_text" placeholder="Post something..."></textarea>
							<div class="clear"></div>
							<input type="submit" class="network post" value="Send"></input>
							<input type="hidden" name="post_class" value="o"></input>
							<input type="hidden" name="post_original" value="NULL"></input>
						</form>
						<hr>
						<ul id="post-wall-ul" class="network">
						<?php 
						foreach($posts as $post) {
							// display post
							echo HTMLBuilder::displayPost($post, $replies[$post->id], 4); 

							/*
							// display replies
							echo HTMLBuilder::displayReplies($replies[$post->id], $post->id, $post->id_network);

							// display reply prompt
							if(isset($_SESSION['uid'])
								&& $post->id == $_GET['pid']) {
								echo HTMLBuilder::displayReplyPrompt($post->id, $user->id, $post->id_network);
							}
							 */
						}
						?>
						</ul>
						<?php if ($pc_data['post_count'] > 10 && !isset($_GET['ap'])) : ?>
						<div>
							<form class="more_posts" method="POST" action="network_more_posts.php">
								<input type="hidden" id="nmp_lb" name="lb" value=10 />
								<input type="hidden" id="nmp_ub" name="ub" value=10 />
								<input type="hidden" id="nmp_nid" name="nid" value="<?php echo $network->id; ?>" />
								<button id="mp_button" class="post show">Load Older Posts</button>
							</form>
						</div>
						<?php endif; ?>
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
					<div id="event-wall" class="tab-pane">
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
						<div>
						<button id="event-post" class="network member" onclick="toggleEventForm()">Post Event</button>
						</div>
						<div class="clear"></div>
						<?php if(isset($_GET['eperror'])) : ?>
						<script>
						</script>
						<?php endif; ?>
						<div id="event-maker">
							<form class="event-form" method="POST" action="network_post-event.php" enctype="multipart/form-data">
								<div>
								<input type="text" id="title" name="title" class="event-text" placeholder="Name of Event " required></input></br>
								<input type="text" id="datetimepicker1" name="datetime" class="event-text datetimepicker" placeholder="Event Date" required>
								<input type="text" class="hidden-field" name="date"></input>
								<input type="text" name="address_1" class="event-text" placeholder="Address 1" required/>
								<input type="text" name="address_2" class="event-text" placeholder="Address 2"/>
								<textarea id="description" name="description" class="event-text" placeholder="What's happening?" required></textarea>
								<div id="clear"></div>
								<input type="text" class="event-text" name="city" placeholder="City" required/></input>
								<input type="text" class="event-text" name="region" placeholder="Region" required/></input>
								<input type="submit" class="network" value="Post"></input>
								<span id="eperror"><?php echo $_GET['eperror']; ?></span>
								</div>
							</form>
						</div>
					</div>
					</br>
					</br>

					</div>
					</br>
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
	<script>
		///// SHOW REPLY ///////////
		function primeShowReplies() {
			$(".show_reply").off("submit");
			$(".show_reply").on("submit", function(e) {
				e.preventDefault();
				// check if replies have already been fetched
				var replies_div = $( e.target ).parent().siblings('div.replies');

				var rd_children = $( replies_div ).children('ul').children();

				if ( rd_children.length <= 4 ) {
					var postForm = $(this).serialize();
					var getReply = new Ajax({
						requestType: 'POST',
							requestUrl: 'network_show_reply.php',
							requestParameters: ' ',
							data: postForm,
							dataType: 'string',
							sendNow: true
					}, function(data) {
						// success function
						var response = JSON.parse(data);
						if (response.error == 0) {
							var targ = e.target;
							$( targ ).children(':submit').val('Hide Replies');
							$( targ ).parent().siblings('div.replies').children('ul').html(response.html);
							primeDeletes();
						}
					}, function(response, rStatus) {

					});
				}
				else {
					if ($( e.target ).children(':submit').val() == 'Hide Replies') {
						$( replies_div ).hide();
						$( e.target ).children(':submit').val('Show Replies');
					}
					else {
						$( replies_div ).show();
						$( e.target ).children(':submit').val('Hide Replies');
						primeDeletes();
					}
				}
			});
		}

		/////////// REQUEST REPLY /////////
		function primeRequestReplies() {
			$(".request_reply").off("submit");
			$(".request_reply").on("submit", function(e) {
				e.preventDefault();
				var postForm = $(this).serialize();
				if( $( e.target ).children('button').text() == 'Reply') {
					var requestReply = new Ajax({
						requestType: 'POST',
							requestUrl: 'network_request_reply.php',
							requestParameters: ' ',
							data: postForm,
							dataType: 'string',
							sendNow: true
					}, function(data) {
						// success function
						var response = JSON.parse(data);
						if (response.error == 0) {
							var targ = e.target;
							$( e.target ).parents('li.network-post').children('div.prompt').show();
							$( targ ).children('button').text('Cancel');
							$( targ ).parents('li.network-post').children('div.prompt').html(response.html);
							primeReplyForms();
						}
					}, function(response, rStatus) {

					});
				}
				else {
					$( e.target ).parents('li.network-post').children('div.prompt').hide();
					$( e.target ).children('button').text('Reply');
				}
			});
		}

		////////// SEND REPLY //////////////
		function primeReplyForms() {
			$( '.reply_form' ).off('submit');
			$( '.reply_form' ).on('submit', function(e) {
				// prevent default behavior
				e.preventDefault();
				// get target
				var targ = e.target;
				var postForm = $( targ ).serialize();

				var sendReply = new Ajax({
					requestType: 'POST',
						requestUrl: 'network_post_reply.php',
						requestParameters: ' ',
						data: postForm,
						dataType: 'string',
						sendNow: true
				}, function(data) {
					// success function
					var response = JSON.parse(data);
					if (response.error == 0) {
						// get div
						var targ = e.target;

						// put all lis out on display
						$( targ ).parents( 'li.network-post' ).children('div.replies').children('ul').html(response.html);

						// activate showreplies button, change to hide replies 
						// get ul
						// if there are over 4 of them
						if( $( targ ).parents( 'li.network-post' ).children('div.replies').children('ul').children().length > 4) {
							$( targ ).parents( 'div.prompt' ).siblings( 'div.show_reply_div' ).children('form.show_reply').show();
							$( targ ).parents( 'div.prompt' ).siblings( 'div.show_reply_div' ).children('form.show_reply').children(':submit').val('Hide Replies');
						}

						// increment replies
						//  a) get reply count
						var replyCount = $( targ ).parents( 'div.prompt' ).siblings('div.replies').children('ul.replies').children().length;
						//  b) increment in obvious place
						$( targ ).parents( 'div.prompt' ).siblings( 'div.post-info' ).children('p.reply_count').text(replyCount + ' replies');
						//  c) increment in hidden submit
						$( targ ).parents( 'div.prompt' ).siblings( 'div.post-info' ).children('form.post_delete').children("input[name='replies']").val(replyCount)
						//  d) increment at top of page

						// activate delete buttons
						primeDeletes();

						// clear out reply thing and change text
						$( targ ).parents( 'div.prompt' ).siblings( 'div.post-info' ).children('div.reply-button').children('form.request_reply').children('button').text('Reply');
						$( targ ).parents( 'div.prompt' ).hide();
					}
				}, function(response, rStatus) {

				});
			});
		}


		////////// DELETE REPLY /////////////
		//
		//
		function primeDeletes() {
			// event handler
			$( '.delete_reply' ).off('submit');
			$( '.delete_reply' ).on('submit', function(e) {
				// prevent default behavior
				e.preventDefault();
				// get target

				if (confirm("Are you sure you want to delete this reply?")) {
					var targ = e.target;
					var postForm = $( targ ).serialize();

					var sendReply = new Ajax({
						requestType: 'POST',
							requestUrl: 'network_reply_delete.php',
							requestParameters: ' ',
							data: postForm,
							dataType: 'string',
							sendNow: true
					}, function(data) {
						// success function
						var response = JSON.parse(data);
						
						if (response.error == 0) {
							// delete parent li
							// get form
							var targ = e.target;
							var ul = $( targ ).parents('ul.replies');

							$( targ ).parents('li.reply').remove();

							// delete whole post
							if (response.status == 'postdelete') {
								$( ul ).parents('li.network-post').remove();
							}
							else {
								//var children = ul.children();
								//var replyCount =  children.length;
								var replyCount = ul.children().length;
								// update reply counts elsewhere
								// post info div
								var div = $( ul ).parents('div.replies').siblings('div.post-info');

								//  1) hidden input on delete post
								$( div ).children('div.reply-button.delete').children('form.post_delete').children("input[name|='replies']").val(replyCount);
								//  2) reply count
								$( div ).children('p.reply_count').text(replyCount + ' replies');

								// update 
								// if ul is now less than 4...
								if ( replyCount <= 4 ) {
									// hide show replies form
									$( ul ).parents('div.replies').siblings('div.show_reply_div').children('form.show_reply').children(':submit').val('Show Replies');
									$( ul ).parents('div.replies').siblings('div.show_reply_div').children('form.show_reply').hide();

									/*
									$( div ).children('div.reply-button').children('form.show_reply').children(':submit').val('Show Replies');
									$( div ).children('div.reply-button').children('form.show_reply').hide();
						 			*/
								}	
							}
						}
					}, function(response, rStatus) {

					});
				}
			});
		}

		/////// DELETE POST ///////////////////
		$('.post_delete').on('submit', function(e) {
			// prevent default behavior 
			e.preventDefault();

			if (confirm("Are you sure you want to delete this post?")) {
				var postForm = $( e.target ).serialize();

				postDelete = new Ajax({
					requestType: 'POST',
						requestUrl: 'network_post_delete.php',
						requestHeaders: ' ',
						data: postForm,
						dataType: 'string',
						sendNow: true
				}, function(data) {
					var response = JSON.parse(data);

					if (response.error == 0) {
						var targ = $( e.target );
						if (response.status == 'destroyed') {
							// remove li 
							targ.parents('li.network-post').remove();
							// decrement number of posts up top
						}

						if (response.status == 'wiped') {
							targ.parents('li.network-post').replaceWith(response.html);
							callPrimes();
						}
					}
				}, function(response, rStatus) {

				});
			}
		});


		function primeDeletePosts() {
			$('.post_delete').off('submit');
			$('.post_delete').on('submit', function(e) {
				// prevent default behavior 
				e.preventDefault();

				if (confirm("Are you sure you want to delete this post?")) {
					var postForm = $( e.target ).serialize();

					postDelete = new Ajax({
						requestType: 'POST',
							requestUrl: 'network_post_delete.php',
							requestHeaders: ' ',
							data: postForm,
							dataType: 'string',
							sendNow: true
					}, function(data) {
						var response = JSON.parse(data);

						if (response.error == 0) {
							var targ = $( e.target );
							if (response.status == 'destroyed') {
								// remove li 
								targ.parents('li.network-post').remove();
								// decrement number of posts up top
							}

							if (response.status == 'wiped') {
								targ.parents('li.network-post').replaceWith(response.html);
								callPrimes();
							}
						}
					}, function(response, rStatus) {

					});
				}
			});
		}

		$('.more_posts').on('submit', function(e) {
			
			// prevent default
			e.preventDefault();

			// get form
			var targ = $(e.target);
			var postForm = $( targ ).serialize();
			
			var requestPosts = new Ajax({
					requestType: 'POST',
					requestUrl: 'network_more_posts.php',
					requestHeaders: ' ',
					data: postForm,
					dataType: 'string',
					sendNow: true
			}, function(data) {
				var response = JSON.parse(data);
				// add stuff to post wall
				$("#post-wall-ul").append(response.html);

				// with new stuff, call primes
				callPrimes();

				if(response['continue'] == 'n') {
					$( targ ).hide();
				}
				else {
					$('#nmp_lb').val(response['lb']);
				}
				
			}, function() {
			});
		});

		function callPrimes() {
			primeShowReplies();
			primeRequestReplies();
			primeReplyForms();
			primeDeletes();
			primeDeletePosts();
			primeShowReplies();
		}

//		primeShowReplies();
//		primeRequestReplies();
		callPrimes();

		console.log("buttons");
	</script>
	<script>
		function switchTab() {
			// remove post active status
			$('#post-nav').removeClass('active');
			$('#post-wall').removeClass('active');

			// add event active status
			$('#event-nav').addClass('active');
			$("#event-wall").addClass('active');
		}
	</script>
	<?php if (isset($_GET['eperror'])): ?>
	<script>
		switchTab();

		toggleEventForm();
		$('#eperror').goTo();
		$('#eperror').text('<?php echo $_GET['eperror']; ?>');
		
	</script>
	<?php endif; ?>
	<script>
		var qs = new QueryStringParser();
		if (qs.qsGet['lerror'] != undefined) {
			if (qs.qsGet['lerror'] == 'success') {
				$('#signout_panel').children('p').text('You are now logged in!');
				$('#signout_panel').show();
				$('#signout_panel').fadeOut(5000);
			}
		}
		if (qs.qsGet['rerror'] != undefined) {
			if (qs.qsGet['rerror'] == 'success') {
				$('#signout_panel').children('p').html('You are now registered! Click <a href="profile_edit.php">here</a> to see your profile page.');
				$('#signout_panel').show();
			}
		}
		if (qs.qsGet['lnerror'] != undefined) {
			$('#lnerror').text(qs.qsGet['lnerror']);
			$('#lnerror').fadeOut(5000);
		}
		if (qs.qsGet['jnerror'] != undefined) {
			$('#jnerror').text(qs.qsGet['jnerror']);
			$('#jnerror').fadeOut(5000);
		}
		if (qs.qsGet['ueerror']!= undefined) {
			var mid = qs.qsGet['eid'];
			switchTab();

			$('#event-modal-'+mid).modal('show');
			$('#ueerror-'+mid).text(qs.qsGet['ueerror']);
		}
		if (qs.qsGet['jeerror']!= undefined) {
			var mid = qs.qsGet['eid'];
			switchTab();

			$('#event-modal-'+mid).modal('show');
			$('#jeerror-'+mid).text(qs.qsGet['jeerror']);
		}
		if (qs.qsGet['elink']!= undefined) {
			switchTab();		
			$('#event-modal-'+ qs.qsGet['elink']).modal('show');

		}
		if (qs.qsGet['leerror'] != undefined) {
			var mid = qs.qsGet['eid'];
			switchTab();

			$('#event-modal-'+mid).modal('show');
			$('#leerror-'+mid).text(qs.qsGet['leerror']);
		}
	</script>
</html>
