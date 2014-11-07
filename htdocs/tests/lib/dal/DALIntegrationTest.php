<?php

class DALIntegrationTest extends PHPUnit_Framework_TestCase {

	public function test() {

		global $cm;

		// db stuff
		$dal = new dal\DAL($cm->getConnection());
		$dal->loadFiles();

		$do2db = new dal\Do2Db();
		$user = dobj\User::testQuery(1, $dal, $do2db);
		$cm->closeConnection();

		$this->assertInstanceOf('dobj\User', $user);
	}
}
