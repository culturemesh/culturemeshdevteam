<?php

$response = array (
	'error' => 0,
	'info' => NULL,
	'tweets' => NULL
);

if (!isset($_POST['location_weight']) || !isset($_POST['origin_weight']) || 
	!isset($_POST['count_weight'])) {

	$response['error'] = 'One of these things was not set you amateur!';
	echo json_encode($response);
	exit();
}

// get post variables
$nid = 352;
$location_weight = (float) $_POST['location_weight'];
$origin_weight = (float) $_POST['origin_weight'];
$count_weight = (float) $_POST['count_weight'];

include '../../environment.php';
$cm = new \Environment();


$dal = new dal\DAL($cm->getConnection());
$dal->loadFiles();
$do2db = new dal\Do2Db();

$network = dobj\Network::createFromId($nid, $dal, $do2db);

$tweet_manager = new \api\TweetManager($cm, $network, $dal, $do2db);
$tweets = $tweet_manager->requestTweets( 'adjust', array(
			'location_weight' => $location_weight,
			'origin_weight' => $origin_weight,
			'count_weight' => $count_weight)
		);

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
