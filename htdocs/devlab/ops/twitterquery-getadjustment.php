<?php

// get post variables
$nid = $_POST['nid'];

include '../../environment.php';
$cm = new \Environment();

$response = array (
	'error' => 0,
	'info' => NULL,
	'tweets' => NULL
);

$dal = new dal\DAL($cm->getConnection());
$dal->loadFiles();
$do2db = new dal\Do2Db();

$network = dobj\Network::createFromId($nid, $dal, $do2db);

$adjustment_control = $_POST['adjustment_control'];
$adjustment = NULL;

$adj = new dobj\TweetQueryAdjustment();


if ($adjustment_control == 'broad') {

	// gets broad level adjustment,
	// must be processed into terms understandable
	// by db
	//
	$adjustment = $_POST['level_adjustment'];
}
if ($adjustment_control == 'fine') {

	$adjustment['query_origin_scope'] = $_POST['origin_scope'];
	$adjustment['query_location_scope'] = $_POST['location_scope'];
	$adjustment['query_since_date'] = $_POST['since_date'];
	$adjustment['query_level'] = $_POST['term_link'];
}

$adj->processAdjustment($network, $adjustment);
$adj->insert($dal, $do2db);
$network->adjustTwitterQuery($dal, $do2db);

$tweet_manager = new \api\TweetManager($cm, $network, $dal, $do2db);
$tweets = $tweet_manager->requestTweets();
$info = $tweet_manager->getQueryInfo();

$cm->closeConnection();

// get the tweet html
$mcomp = new misc\MustacheComponent();
$tweets->setMustache( $mcomp );
$tmp = file_get_contents($cm->template_dir . $cm->ds . 'network-postwall.html');

try {
	$response['html'] = $tweets->getHTML('network', array(
						'cm' => $cm,
						'network' => $network,
						'mustache' => $mcomp,
						'list_template' => $tmp
					)
				);
}
catch (\Exception $e) {
	$response['html'] = NULL;
	$response['error'] = 'No tweets were found with query.';
}

$response['info'] = array(
	'title' => $network->getTitle(),
	'level' => $network->query_level,
	'origin_term' => $network->getQueryOriginComponent(),
	'origin_scope' => $network->getOriginScope(),
	'query_origin_scope' => $network->query_origin_scope,
	'location_term' => $network->getQueryLocationComponent(),
	'location_scope' => $network->getLocationScope(),
	'min_origin' => $network->getMinOriginScope(),
	'max_origin' => $network->getOriginScope(),
	'min_location' => $network->getMinLocationScope(),
	'max_location' => $network->getLocationScope(),
	'query_location_scope' => $network->query_location_scope,
	'query_relevance' => $info['relevance'],
	'max_count' => $info['max_count'],
	'result_count' => $info['result_count'],
	'since_date' => $info['since_date'],
	'query' => $info['query']
);

echo json_encode($response);

?>
