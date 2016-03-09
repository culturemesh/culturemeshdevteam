<?php

// get post variables
$nid = $_POST['nid'];

include '../../environment.php';
$cm = new \Environment();

$response = array (
	'error' => 0,
	'info' => NULL,
	'network' => NULL,
	'html' => NULL
);

$dal = new dal\DAL($cm->getConnection());
$dal->loadFiles();
$do2db = new dal\Do2Db();

//delete
$log = new misc\Log($cm, 'devlab/ops/twitter-query-error.log');

$network = dobj\Network::createFromId($nid, $dal, $do2db);

// delete
$log->logVar($network);

$tweet_manager = new \api\TweetManager($cm, $network, $dal, $do2db);
$tweets = $tweet_manager->requestTweets();
//echo count($tweets);
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
	'query_location_scope' => $network->query_location_scope,
	'min_level_distance' => $network->getDistanceToMinLevel(),
	'max_level_distance' => $network->getDistanceToMaxLevel(),
	'min_origin' => $network->getMinOriginScope(),
	'max_origin' => $network->getOriginScope(),
	'min_location' => $network->getMinLocationScope(),
	'max_location' => $network->getLocationScope(),
	'min_level' => $network->getMinLevel(),
	'max_level' => $network->getMaxLevel(),
	'query_relevance' => $info['relevance'],
	'max_count' => $info['max_count'],
	'result_count' => $info['result_count'],
	'since_date' => $info['since_date'],
	'query' => $info['query'],
	'component_string' => $info['component_string']
);

$response['network'] = array(
	'id' => $network->id
);

echo json_encode($response);

?>
