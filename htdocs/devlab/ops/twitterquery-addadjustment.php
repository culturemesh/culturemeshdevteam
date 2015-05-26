<?php

include '../../environment.php';
$cm = new \Environment();

$nid = $_POST['nid'];

$dal = new dal\DAL($cm->getConnection());
$dal->loadFiles();
$do2db = new dal\Do2Db();

$network = dobj\Network::createFromId(352, $dal, $do2db);

$data = array(
	'query_location_scope' => 1,
	'query_level' => 2,
	'prev_query_relevance' => 0.46,
	'query_since_date' => '2009-01-01'
);

$adjustment = new dobj\TweetQueryAdjustment();
$adjustment->processAdjustment($network, $data);
$adjustment->insert($dal, $do2db);
$network->adjustTwitterQuery($dal, $do2db);

$cm->closeConnection();

echo 'completed';

?>
