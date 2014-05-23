<?php
//	ini_set('display_errors', true);
//	error_reporting(E_ALL ^ E_NOTICE);
	include "log.php";
	include "search_query.php";
	include "html_builder.php";
	include_once "data/dal_user.php";
	
	session_name("myDiaspora");
	session_start();

	/* Most of this overhead stuff
	 * is error checking/query parsing
	 */

	/* Query results takes the actual results
	 * and makes networks out of them
	 */

	function parseLocation($location)
	{
		/*
		$loc_split = null;
		for ($i = 0; $i < strlen($location); $i++) 
		{
			if (($location[$i] === ",") && ($location[$i + 1] === " "))
			{
				$loc_split = array(substr($location, 0, $i), substr($location, $i + 2, strlen($location)));	
				break;
			}
		}

		if ($loc_split == null)
			return $location;
		else
			return $loc_split;
		 */
		$loc_array = explode(", ", $location);

		if (count($loc_array) == 2)
		  { $loc_array = array(null, $loc_array[0], $loc_array[1]); }
		else if (count($loc_array) == 1)
		  { $loc_array = array(null, null, $loc_array[0]); }
		return $loc_array;
	}

	function validateQuery($query, $con) 
	{
		// check for form	
		$prompts = array("Find people who speak ",
			"Find people who are from ");
		$prompt_index = null;

		// check which prompt we're dealing with
		// extract query from it as $value
		for ($prompt_index = 0; $prompt_index < count($prompts); $prompt_index++)
		{
			if (stristr($query, $prompts[$prompt_index]))
			{
				// break out query value from prompt
				$value = substr($query, strlen($prompts[$prompt_index]));
				break;
			}
		}

		if (!$value)
		  { return false; }
		else if (strlen($value) < 1)
		  { return false; }

		// get all search values
		$values = explode(", ", $value);
		
		if (count($values) <= 1)
		{
			// we're dealing with language or country
			// check prompt str
			// 0 - language
			// 1 - country
			if ($prompt_index == 0)
			  { return SearchQuery::checkValue($value, "language", $con); }
			else if ($prompt_index == 1)
			  { return SearchQuery::checkValue($value, "country", $con); }
		}
		else
		{ 
			// we've got a city or a region
			// cities 
			if (count($values) == 2)
			{
				if( SearchQuery::checkValue($values[0], "city/region", $con)) 
				  { return SearchQuery::checkValue($values[1], "country", $con); }
			}
			if (count($values) == 3)
			{
				if( SearchQuery::checkValue($values[0], "city/region", $con)) {
					if ( SearchQuery::checkValue($values[1], "city/region", $con))
				  		{ return SearchQuery::checkValue($values[2], "country", $con); }
				}
			}
		}
	} 

	/*
		- Checkforerrors
			PART ONE
			+ checksum for first part, 'Find people who'
			+ check for verbs (and mispellings)
			+ check for descriptor
			--------------
			PART TWO
			+ check location for errors
			
		BEST CASE (skewed American because I'm american, dammit): 
			- people type action verb correctly,
			- people spell correctly in English
			- people remember to include spaces
			- just a general lack of typos
		
		Array of NetworkDts
		[0] Best match
		[1:4] - Other Suggestions
	*/
	if (isset($_GET["error"]))
	  { }
	else
	{
		// get topic from GET
		if (isset($_GET['search-topic']))
			$type = $_GET['search-topic'];

		$queries = array("Find people who speak", "Find people who are from");
		
		$con = getDBConnection();
		$people_who = mysqli_real_escape_string($con, $_GET['search-1']);
		$valid_search = validateQuery($people_who, $con);

		// get rid of 'near ' in location
		//$location_raw = mysql_escape_string(substr($_GET['search-2'], 5));

		// get rid of 'in ' in location
		$location_raw = mysqli_real_escape_string($con, substr($_GET['search-2'], 3));
		
		// now separate into city and country, if possible
		$location = explode(", ", $location_raw);

		if (count($location) == 2)
		  { $location = array(null, $location[0], $location[1]); }
		if (count($location) == 1)
		  { $location = array(null, null, $location[0]); }

		$query_token = strtok($people_who, " ");
		
		// SOMEWHERE IN HERE, I'LL PROBABLY HAVE TO PROCESS THE QUERY TO MAKE SURE THE 
			// THE PROGRAM WILL KNOW WHAT TO DO

		// Loop through the tokens of the string,
		// until we've got the query	
		while ($query_token != false)
		{
			if ($query_token == "speak")
			{
				$topic = "language";
				$query_token = strtok(" ");
				break;
			}
			
			if ($query_token == "are")
			{
				$query_token = strtok(" ");
				
				if ($query_token == "from")
				{
					$topic = "origin";
					$query_token = strtok("");
					break;
				}	
			}
			
			$query_token = strtok(" ");
		}
		
		$query_str = "";
		$query = null;
		
		if ($type == "_l")
		{
			// probably the name of the language will be only one token
			$query_str = $query_token;
			//echo $query_str."\n";
			$query = array($type, $location[0], $location[1], $location[2], $query_str);
		}
		if ($type == "co")
		{
			$query_str = $query_token;
			$origin = parseLocation($query_str);
			//var_dump($location);
			//var_dump($origin);
			if (gettype($origin) == "array")
				$query = array($type, $location[0], $location[1], $location[2], $origin[0], $origin[1], $origin[2]);
			else
				$query = array($type, $location[0], $location[1], $location[2], $origin);
		}
		if ($type == "rc" || $type == "cc")
		{
			$query_str = $query_token;
			$origin = parseLocation($query_str);
			//var_dump($location);
			//var_dump($origin);
			//echo $origin[0]."\n".$origin[1]."\n";
			$query = array($type, $location[0], $location[1], $location[2], $origin[0], $origin[1], $origin[2]);
		}

		// with location, query_str, and topic calculated, get the information
		// for filling in the site
		//
		$networks = SearchQuery::getNetworkSearchResults($query, $con);
		mysqli_close($con);

		// add location data for gmaps embed
		$location = '';
		if ($valid_search)
		{
			if (isset($query[1]))
			   { $location .= urlencode($query[1]).','; }
			if (isset($query[2]))
			   { $location .= urlencode($query[2]).','; }
			if (isset($query[3]))
			   { $location .= urlencode($query[3]); }
		}
	}

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
	<body>
		<div class="wrapper">
			<?php
				include "header.php";
			?>
			<div id="content">
				<div class="net-left">
					<div class="leftbar">
						<?php HTMLBuilder::googleMapsEmbed($location); ?>
<!--
						<div class="map">
							<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d202
							359.87608164057!2d-122.32141071468104!3d37.581606634196056!2m3!1f0!2f0!
							3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808fcae48af93ff5%3A0xb99d8c0aca9f717b
							!2sSan+Jose%2C+CA!5e0!3m2!1sen!2sus!4v1389926877454" width="100%" height="252"
							frameborder="0" style="border:0"></iframe>
						</div>
-->
						<div id="tags"></div>
					</div>
				</div>
				<div class="net-right">
					<?php HTMLBuilder::displaySearchBar(); ?>
					<div id="sr-error"><p><?php if( isset($_GET['error'])) echo $_GET['error']; ?></p></div>
					<div id="invalid-search"><p><?php if(!$valid_search) echo "There are no networks that match. Try another search"; ?></p></div>
					<div id="best-match">
						<h3>Best Match</h3>
						<?php 
						if(!$network[0]->existing)
						  { HTMLBuilder::displayPossibleNetwork($networks[0]); }
						else if (!$valid_search)
						  { echo "No matches found."; }
						else 
						  { HTMLBuilder::displayNetwork($networks[0]); }
						?>
					</div>
					<div id="related-networks">
						<h3>Related Networks</h3>
						<?php for ($i = 1; $i < count($networks); $i++)
							{
								if ($networks[$i]->existing)
								  { HTMLBuilder::displayNetwork($networks[$i]); }
								else
								  { HTMLBuilder::displayPossibleNetwork($networks[$i]); }
							}
						?>
						<?php if (count($networks) < 2) : ?>
							<p>No related networks</p>
						<?php endif; ?>
						<?php/* else : 
							for ($i=1; $i < count($networks); $i++)
							{
								HTMLBuilder::displayNetwork($networks[$i]);
							}
							endif;
						 */
						?>
					</div>
				</div>
			</div>
			<div class="clear"></div>
			<?php
				include "footer.php";
			?>
		</div>
	</body>
	<script src="js/searchbar.js"></script>
</html>
