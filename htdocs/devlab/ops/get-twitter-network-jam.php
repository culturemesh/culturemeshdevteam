<?php
include('../../environment.php');
$cm = new Environment();

$nid = $_POST['nid'];

$dal = new \dal\DAL($cm->getConnection());
$dal->loadFiles();
$do2db = new \dal\Do2Db();
$network = \dobj\Network::createFromId($nid, $dal, $do2db);

echo json_encode($network->getTwitterApiInfo());

?>
