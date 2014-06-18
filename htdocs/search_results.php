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

		$query = SearchQuery::buildQuery($search_1, $search_2,
						$topic, $verb,
						$clik1, $clik2, $con);

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
					<div id="search-report">
						<p>You searched for <?php echo $search_1 . ' at ' . $search_2; ?></p>
						<p>Displaying results for <?php echo $query[4] .' '. $query[5] .' '. $query[6] . ' at ' . $query[1] .' '. $query[2] .' '. $query[3]; ?></p>
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
