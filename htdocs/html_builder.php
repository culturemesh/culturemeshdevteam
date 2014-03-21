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
		$title = null;
		switch ($query[0])
		{
		case "_l":
			$title = "{$query[3]} speakers near {$query[1]}, {$query[2]}";
			break;
		case "co":
			$title = "From {$query[3]} near {$query[1]}, {$query[2]}";
			break;
		case "rc":
			$title = "From {$query[3]}, {$query[4]}  near {$query[1]}, {$query[2]}";
			break;
		case "cc":
			$title = "From {$query[3]}, {$query[4]} near {$query[1]}, {$query[2]}";
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
					<input type='hidden' name=country_cur value='{$query[2]}'/>
					<input type='hidden' name=q_1 value='{$query[3]}'/>
					<input type='hidden' name=q_2 value='{$query[4]}'/>
				</form>
			</div>
			<div class='clear'></div>
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
