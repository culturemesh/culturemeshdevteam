<?php
//	ini_set('display_errors', true);
//	error_reporting(E_ALL ^ E_NOTICE);
//	include "log.php";
	include "search_query.php";
	include "html_builder.php";
	include_once "data/dal_user.php";
	include "Environment.php";
	
	session_name("myDiaspora");
	session_start();

	/* Most of this overhead stuff
	 * is error checking/query parsing
	 */

	/* Query results takes the actual results
	 * and makes networks out of them
	 */

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
		// get get variables
		$con = getDBConnection();
		$search_1 = mysqli_real_escape_string($con, $_GET['search-1']);
		$search_2 = mysqli_real_escape_string($con, $_GET['search-2']);
		$topic = mysqli_real_escape_string($con, $_GET['search-topic']);
		$verb = mysqli_real_escape_string($con, $_GET['verb']);

		// get the clicked values
		$clik1 = mysqli_real_escape_string($con, $_GET['clik1']);
		$clik2 = mysqli_real_escape_string($con, $_GET['clik2']);

		$response = SearchQuery::buildQuery($search_1, $search_2,
						$topic, $verb,
						$clik1, $clik2, $con);

		$query = $response[0];

		if ($response[1] && $response[2])
			$networks = SearchQuery::getNetworkSearchResults($query, $con);

		mysqli_close($con);

		// add location data for gmaps embed
		$location = '';

		if (isset($query[1]))
		   { $location .= urlencode($query[1]).','; }
		if (isset($query[2]))
		   { $location .= urlencode($query[2]).','; }
		if (isset($query[3]))
		   { $location .= urlencode($query[3]); }
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
						<div id="tags"></div>
					</div>
				</div>
				<div class="net-right">
					<?php HTMLBuilder::displaySearchBar(); ?>
					<div id="sr-error"><p><?php if( isset($_GET['error'])) echo $_GET['error']; ?></p></div>
					<div id="search-report">
						<?php if($clik1 + $clik2 < 2): ?>
						<p class='search-result'>You searched for <?php echo $search_1 . ' at ' . $search_2; ?></p>
						<p class='search-result'>Displaying results for <?php echo HTMLBuilder::formatQueryTitle($query); ?></p>
						<?php endif; ?>
					</div>
					<div id="best-match">
						<h3>Best Match</h3>
						<?php 
						if(!$networks[0]->existing)
						  { HTMLBuilder::displayPossibleNetwork($query); }
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
