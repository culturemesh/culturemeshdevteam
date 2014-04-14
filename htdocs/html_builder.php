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
		echo "
		<li class='network-post'>
			<div class='post-img'>
				<img id='profile-post' src='images/blank_profile.png' width='45' height='45'>
			</div>
			<div class='post-info'>
				<h5 class='h-network'>{$post->email}</h5>
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
				<p id='event-info'>Hosted by {$event->email} and set for {$datetime}</p>
				<p id='event-desc'>{$event->description}</p>
				
			</div>
		</li>
		";
	}

	public static function displayEventMonth($month)
	{
		echo "
		<td class='event-card month'>
			<p>{$month}</p>
		</td>
		";
	}

	public static function displayEventCard($event)
	{
		$datetime = strtotime($event->event_date);
		$datetime = date("m/d/y g:i", $datetime);
		
		echo "
		<td class='event-card card'>
			<div >
				<h3 class='h-network'>{$event->title}</h3>
			</div>
			<div class='card-content'>
				<div class='card-img'>
					<img src='images/background-placeholder.png' alt='No image'></img>
				</div>
				<div class='card-info'>
					<p id='event-info'>With {$event->email}</p>
					<p id='event-date'>{$datetime}</p>
				</div>
			</div>
			<div class='clear'></div>
		</td>
		";

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
}
?>
