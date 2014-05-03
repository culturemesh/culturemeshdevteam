<?php

class HTMLBuilder
{
	public static function displayPopNetwork($network)
	{
		$title = HTMLBuilder::formatNetworkTitle($network);
		$post_count = $network->post_count;
		$member_count = $network->member_count;
		
		if ($post_count == NULL)
			$post_count = 0;
		if ($member_count == NULL)
			$member_count = 0;
		// Print network
		echo "
		<div id='pn_{$network->id}' class='popnet'>
			<div class='popnet-info'>
				<a href='network.php?id={$network->id}'><p class='bottom-text'>{$title}</p></a>
				<p class='network-stats'>{$member_count} Members | {$post_count} Posts</p>
			</div>
			<div class='clear'></div>
		</div>
		";
	}
	
	public static function displayNetwork($network)
	{
		$title = HTMLBuilder::formatNetworkTitle($network);
		
		echo "
		<div>
			<div class='net-info'>
				<a href='network.php?id={$network->id}'><p class='bottom-text'>{$title}</p></a>
				<p class='network-stats'>{$network->member_count} Members | {$network->post_count} Posts</p>
			</div>
			<div class='clear'></div>
		</div>
		";
	}

	public static function displayPossibleNetwork($query)
	{
		// locations start at 1, have three addresses reserved (may be NULL)
		// rest is variable
		$query_length = count($query);
		$title = null;
		$location = null;
		$query_str = null;
		$location_start = 1;
		$location_end = 4;

		// get current location
		for ($i = $location_start; $i < $location_end; $i++)
		{
			if ($query[$i] != null)
			{ 
				$location .= $query[$i];
				if ( $location_end  - $i != 1)
				  { $location .= ", "; }
		       	}

		}

		// get origin
		if ($query[0] != "_l")
		{
			for ($i = $location_end; $i < count($query); $i++)
			{
				if ($query[$i] != null)
				{
					$query_str .= $query[$i];
					if (count($query) - $i != 1)
					  { $query_str .= ", "; }
				}
			}
		}

		switch ($query[0])
		{
		case "_l":
			$title = "{$query[4]} speakers in {$location}";
			break;
		case "co":
			$title = "From {$query_str} in {$location}";
			break;
		case "rc":
			$title = "From {$query_str} in {$location}";
			break;
		case "cc":
			$title = "From {$query_str} in {$location}";
			break;
		}

		echo "
		<div>
			<div class='net-info'>
				<form method='POST' action='search_launch_network.php'> 
					<p class='bottom-text'>{$title}</p>
					<input type='submit' class='launch-network' value='Launch Network'></input>
					<input type='hidden' name=type value='{$query[0]}'/>
					<input type='hidden' name=city_cur value='{$query[1]}'/>
					<input type='hidden' name=region_cur value='{$query[2]}'/>
					<input type='hidden' name=country_cur value='{$query[3]}'/>
					<input type='hidden' name=q_1 value='{$query[4]}'/>
					<input type='hidden' name=q_2 value='{$query[5]}'/>
					<input type='hidden' name=q_3 value='{$query[6]}'/>
				</form>
			</div>
			<div class='clear'></div>
		</div>
		";
	}

	public static function displaySearchBar()
	{
		echo "
		<div id='search'>
			<form id ='search-form' method='GET' action='search_results.php' autocomplete='off'>
				<input type='text' id='search-1' class='net-search' name='search-1' value='Find people who ' autocomplete='off'/>
					<ul id='s-query' class='network search'></ul>
					<ul id='s-var' class='network search'></ul>
				<input type='text' id='search-2' class='net-search' name='search-2' value='In ' autocomplete='off'/>
					<ul id='s-location' class='network search'></ul>
				<input type='submit' class='network search-button' value='Go'>
				<input type='hidden' id='search-topic' name='search-topic'></input>
			</form>
		</div>
		";
	}
	
	public static function displayLrgNetwork($network)
	{
		$title = HTMLBuilder::formatNetworkTitle($network);
		
		echo "
		<div>
			<div class='net-info'>
				<h1 class='h-network'>{$title}</h1>
				<p class='lrg-network-stats'>{$network->member_count} Members | {$network->post_count} Posts</p>
			</div>
			<div class='clear'></div>
		</div>
		";
	}
	
	public static function displayPost($post)
	{
		$name = NULL;
		if ($post->first_name == '')
			//$name = $post->email;
			$name = "UNNAMED USER";
		else {
			$name = $post->first_name;
			if (isset($post->last_name))
				$name .= " ".$post->last_name;
		}
		echo "
		<li class='network-post'>
			<div class='post-img'>
				<img id='profile-post' src='images/blank_profile.png' width='45' height='45'>
			</div>
			<div class='post-info'>
				<h5 class='h-network'>{$name}</h5>
				<p class='network'>{$post->post_text}</p>
				<a class='network member'>Reply</a>
			</div>
			<div class='clear'></div>
		</li>
		";
	}
	
	public static function displayEvent($event)
	{
		$datetime = strtotime($event->event_date);
		$datetime = date("m/d/y g:i", $datetime);
		
		echo "
		<li class='event'>
			<div class=''>
				<h3 class='h-network'>{$event->title}</h3>
			</div>
			<div class=''>
				<p id='event-info'>Hosted by {$event->first_name} {$event->last_name} and set for {$datetime}</p>
				<p id='event-desc'>{$event->description}</p>
				
			</div>
		</li>
		";
	}

	public static function displayEventMonth($month, $year)
	{
		echo "
		<td class='event-card month'>
			<p>{$month}</p>
			<p>20{$year}</p>
		</td>
		";
	}

	public static function displayEventCard($event)
	{
		$name = NULL;
		if ($event->first_name == '')
			//$name = $event->email;
			$name = "UNNAMED USER";
		else {
			$name = $event->first_name;
			if (isset($event->last_name))
				$name .= " ".$event->last_name;
		}

		$datetime = strtotime($event->event_date);
		$datetime = date("m/d/y g:i", $datetime);

		$datetime = self::formatDateTime($datetime);
		
		//echo "
		$card = <<<EHTML
		<td class='event-card card'>
			<div>
				<h3 class='h-network'>$event->title</h3>
			</div>
			<div class='card-content'>
				<div class='card-img'>
					<img src='images/background-placeholder.png' alt='No image'></img>
				</div>
				<div class='card-info'>
					<p id='event-info'>With $name</p>
					<p id='event-date'>$datetime</p>
					<a data-toggle="modal" href="#event-modal-$event->id">More Info</a>
				</div>
			</div>
			<div class='clear'></div>
		</td>
EHTML;

		echo $card;
	}

//////////////////////
public static function displayEventModal($event) {
//////////////////////

// this is split into three parts
// right now, not exactly pretty,
// but I need it to make a part of the
// template conditional

$modal_1 = <<<EHTML
<div id="event-modal-$event->id" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="blogPostLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
	</div>
	<div class="modal-body event">
		<div id="event-modal-info-$event->id" class="modal-event info">
		    <form class="event-modal-form" method="POST" action="network_update-event.php" >
			<div class="event-modal-section">
			<h1 class="h-network nomargin info-$event->id">$event->title</h1>
				<input type="text" id="title" name="title" class="event-text-modal edit-$event->id" placeholder="Name of Event " value="$event->title"></input>
			</div>
			<div class="event-modal-section">
			<h3 class="h-network">With $event->first_name $event->last_name</h3>
			<p class="event-modal info-$event->id">$event->description</p>
				<textarea id="description" name="description" class="event-text-modal edit-$event->id" placeholder="What's happening?">$event->description</textarea>
			</div>
			<div class="event-modal-section">
			<h3 class="h-network">Date</h3>
				<input type="text" id="datetimepicker" class="event-text-modal edit-$event->id datetimepicker" name="datetime" class="event-text" placeholder="Event Date" value="$event->event_date">
				<input type="text" class="hidden-field" name="date"></input>
			<p class="event-modal info-$event->id">$event->event_date</p>
			</div>
			<div class="event-modal-section">
			<h3 class="h-network">Address</h3>
			<p class="event-modal info-$event->id">$event->address_1</p>
				<input type="text" name="address_1" class="event-text-modal edit-$event->id" placeholder="Address 1" value="$event->address_1"/>
			<p class="event-modal info-$event->id">$event->address_2</p>
				<input type="text" name="address_2" class="event-text-modal edit-$event->id" placeholder="Address 2" value="$event->address_2"/>
			<p class="event-modal info-$event->id">$event->city</p>
			<p class="event-modal info-$event->id">$event->country</p>
			</div>
				<input type="submit" class="submit edit-$event->id" value="Submit Changes"></input>
		    </form>
		</div>
EHTML;

$modal_anchor = NULL;
if ($_SESSION['uid'] == $event->id_host)
{
$modal_anchor= <<<EHTML
<!--
		<div id="event-modal-form-$event->id" class="modal-event edit owner" style="display:none;">
				<div>
				<div class="event-modal-section">
				<h3 class="h-network">Title</h3>
				</div>
				<div class="event-modal-section">
				<h3 class="h-network">Date & Time</h3>
				</div>
				<div class="event-modal-section">
				<h3 class="h-network">Address</h3>
				</div>
				<div class="event-modal-section">
				</div>
				<div id="clear"></div>
				<input type="hidden" class="hidden-field" name="id_event" value="$event->id"/>
				<input type="text" class="hidden-field" name="city" value="$network->city_cur"/></input>
				<input type="text" class="hidden-field" name="country" value="$network->country_cur"/></input>
				</div>
		</div>
-->
		<a id="event-modify-toggle-$event->id" class="">Edit Event</a> 
EHTML;
}

$modal_2 = <<<EHTML
	</div>
<!--
	<div class="modal-footer">
		<p>Footer</p>
	</div>
-->
</div>
<script>
	// all the variables
	//var info$event->id = document.getElementById("event-modal-info-$event->id");
	var link$event->id = document.getElementById("event-modify-toggle-$event->id");
	if (link$event->id != null)
	{
		//var form$event->id = document.getElementById("event-modal-form-$event->id");
		//var editList$event->id = document.getElementsByClassName("");
		//var displayList$event->id = document.getElementsByClassName("");
		var info$event->id = document.getElementsByClassName("info-$event->id");
		var form$event->id = document.getElementsByClassName("edit-$event->id");

		link$event->id.onclick = function() {
			if (form$event->id[0].style.display == "none") { 
				link$event->id.innerHTML = "Cancel Changes";
				for (var i = 0; i < form$event->id.length; i++) {
					form$event->id[i].style.display = "block";
				}
				for (var i = 0; i < info$event->id.length; i++) {
					info$event->id[i].style.display = "none";
				}
			}
			else {
				link$event->id.innerHTML = "Edit Event";
				for (var i = 0; i < form$event->id.length; i++) {
					form$event->id[i].style.display = "none";
				}
				for (var i = 0; i < info$event->id.length; i++) {
					info$event->id[i].style.display="block";
				}
			}
		}
	}
</script>
EHTML;

// DISPLAY THE STUFFFFF!!!!
echo $modal_1 . $modal_anchor . $modal_2;
}
	
	/**************** USER DASHBOARD STUFF 	********************/
	public static function displayDashPost($post)
	{
		echo "
		<li class='network-post dashboard'>
			<div class='post-img'>
				<img id='profile-post' src='images/blank_profile.png' width='45' height='45'>
			</div>
			<div class='post-info'>
				<h5 class='h-network'>{$post->email}</h5>
				<p class='network'>{$post->post_text}</p>
			</div>
			<div class='clear'></div>
		</li>
		";
	}
	
	public static function displayDashEvent($event)
	{
		$datetime = strtotime($event->event_date);
		$datetime = date("m/d/y g:i", $datetime);
		
		echo "
		<li class='event dashboard'>
			<div class='event-host'>
				<img src='images/blank_profile.png' width='72' height='72'/>
			</div>
			<div class='event-text'>
				<div class='event-title'>
					<h3 class='h-network'>{$event->title}</h3>
				</div>
				<div class='event-info'>
					<p id='event-info'>Hosted by {$event->email} and set for {$datetime}</p>
					<p id='event-desc'>{$event->description}</p>
					
				</div>
			</div>
			<div class='clear'></div>
		</li>
		";
	}
	
	public static function displayDashNetwork($network)
	{
		$title = HTMLBuilder::formatNetworkTitle($network);
		
		echo "
		<div class='net-info dashboard'>
			<a href='network.php?id={$network->id}'><p class='bottom-text dashboard'>{$title}</p></a>
			<p class='network-stats'>{$network->member_count} Members | {$network->post_count} Posts</p>
			<p class='network-stats'>Joined {$network->join_date}</p>
		</div>
		";
	}
	
	public static function displayDashConversation($conversation)
	{
		$email = "";
		// check to see which email object should be used, user 1 or user 2
		if ($conversation->id_user2_dt == null)
			$email = $email.$conversation->id_user1_dt->email;
		else
			$email = $email.$conversation->id_user2_dt->email;
			
		echo "
		<div class='user-img dashboard'>
			<img src='images/blank_profile.png' width='72' height='72'/>
		</div>
		<div class='user-info dashboard'>
			<h3 class='h-network'>{$email}</h3>
			<p class='bottom-text dashboard'>{$conversation->start_date}</p>
		</div>
		<div class='clear'></div>
		";
	}
	
	public static function formatNetworkTitle($network)
	{
		$title = '';
		$location = '';
		
		if ($network->city_cur != null)
			$location .= $network->city_cur . ", ";
		if ($network->region_cur != null)
			$location .= $network->region_cur . ", ";

		$location .= $network->country_cur;
		
		switch($network->network_class)
		{
		case '_l':	// LANGUAGE
			$title = "{$network->language_origin} speakers in {$location}";
			break;
		case 'cc':	// CITY,REGION
			$title = "From {$network->city_origin}, {$network->country_origin} in {$location}";
			break;
		case 'rc':	// REGION
			$title = "From {$network->region_origin}, {$network->country_origin} in {$location}";
			break;
		case 'co':	// COUNTRY
			$title = "From {$network->country_origin} in {$location}";
			break;
		}
		
		return $title;
	}

	private static function formatDateTime($date)
	{
		$months = array("NOTHING", "January", "February", "March", "April",
			"May", "June", "July", "August", "September",
			"October", "November", "December");

		// turn date into assoc array
		$p_date = date_parse($date);

		// assign month
		$month = $months[$p_date['month']];

		// handle hours
		$hour = $p_date['hour'];
		$period = "AM";
		if ($hour > 12) {
			$hour -= 12;
			$period = "PM";
		}

		// check minute
		$minute = $p_date['minute'];
		if ($minute < 10)
			$minute = '0'.$minute;

		// format and return string
		$f_datetime = $month." ".$p_date['day'].", ".$p_date['year']." @ ".$hour.":".$minute." ".$period;
		return $f_datetime;
	}
}
?>
