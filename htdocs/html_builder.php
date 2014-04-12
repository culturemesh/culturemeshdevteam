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
		
		for ($i = 1; $i < 4; $i++)
		{
			$location = $location.$query[$i];
			if ($i < 3)
			  { $location = $location . ", "; }
		}

		switch ($query_length)
		{
		case 5:
			$query_str = $query[4];
			break;
		case 6:
			$query_str = $query[4] . ", " . $query[5];
			break;
		case 7:
			$query_str = $query[4] . ", " . $query[5] . ", " . $query[6];
			break;
		}

		switch ($query[0])
		{
		case "_l":
			$title = "{$query_str} speakers near {$location}";
			break;
		case "co":
			$title = "From {$query_str} near {$location}";
			break;
		case "rc":
			$title = "From {$query_str} near {$location}";
			break;
		case "cc":
			$title = "From {$query_str} near {$location}";
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
				<input type='text' id='search-2' class='net-search' name='search-2' value='Near ' autocomplete='off'/>
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
	
	public static function formatNetworkTitle($network)
	{
		$title = '';
		
		switch($network->network_class)
		{
		case '_l':	// LANGUAGE
			$title = "{$network->language_origin} speakers in {$network->city_cur}, {$network->country_cur}";
			break;
		case 'cc':	// CITY,REGION
			$title = "From {$network->city_origin}, {$network->country_origin} in {$network->city_cur}, {$network->country_cur}";
			break;
		case 'rc':	// REGION
			$title = "From {$network->region_origin}, {$network->country_origin} in {$network->city_cur}, {$network->country_cur}";
			break;
		case 'co':	// COUNTRY
			$title = "From {$network->country_origin} in {$network->city_cur}, {$network->country_cur}";
			break;
		}
		
		return $title;
	}
}
?>
