<?php

class DALIntegrationTest extends PHPUnit_Framework_TestCase {

	public function test() {

		global $cm;

		// db stuff
		$dal = new dal\DAL($cm->getConnection());
		$cm->closeConnection();
		/*
		$dal->loadFiles();
		$do2db = new dal\Do2Db();
		
		$user = dobj\User::testQuery(1, $dal, $do2db);
		var_dump($user);


		$this->assertInstanceOf('dobj\User', $user);
		 */
	}
}
