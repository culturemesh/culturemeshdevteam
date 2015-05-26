<?php

include '../../environment.php';
$cm = new \Environment();

$nid = $_POST['nid'];

$dal = new dal\DAL($cm->getConnection());
$dal->loadFiles();
$do2db = new dal\Do2Db();

$network = dobj\Network::createFromId($nid, $dal, $do2db);

$adjustment_control = $_POST['adjustment_control'];
$adjustment = array();

$adjustment['query_origin_scope'] = $_POST['origin_scope'];
$adjustment['query_location_scope'] = $_POST['location_scope'];
$network->adjustTwitterQueryFine($dal, $do2db, $adjustment);

$cm->closeConnection();

echo 'completed';
?>
