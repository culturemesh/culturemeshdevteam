<?php
namespace control;

class Network {

	public static function test($cm, $params) {
		echo 'Test chamber';
	}

	/*
	 * Network Id has been input for search n stuff
	 *
	 */
	public static function match($cm, $params) {

		$id = $params['id'];

		// prepare for db
		$dal = new \dal\DAL($cm->getConnection());
		$dal->loadFiles();
		$do2db = new \dal\Do2Db();

		// load network
		$network = \dobj\Network::createFromId($id, $dal, $do2db);

		// 404 Redirect
		if ($network == False) {
			header('Location: ' . $cm->host_root . $cm->ds . '404.php');
		}
		
		var_dump($network);

		// close connection
		$cm->closeConnection();
	}
}

?>
