<?php
//namespace db;
require_once('Environment.php');

class UserTest extends db\DatabaseTest
{
	public function __construct() {

		$this->self_fixtures = new misc\TestFixture(array(
			'users' => array()));
	}

	public function testDataBaseConnection() {

		$this->getConnection()->createDataSet(array('users'));
		//$prod = $this->getDataSet();
		$queryTable = $this->getConnection()->createQueryTable(
			'users', 'SELECT * FROM users'
		);

		$fs = new misc\TestFixture(array(
			'users' => array()));
		
		$expectedTable = $this->getDataSet($fs)->getTable('users');

		// check data in xml file
		$this->assertTablesEqual($expectedTable, $queryTable);
	}

	public function testDataBaseConnection2() {

		$this->getConnection()->createDataSet(array('users', 'users_id_select'));
		//$prod = $this->getDataSet();
		$queryTable = $this->getConnection()->createQueryTable(
			'users', 'SELECT * FROM users WHERE id=1'
		);

		$fs = new misc\TestFixture(array(
			'users' => array('users_id_select')));

		$expectedTable = $this->getDataSet($fs)->getTable('users');

		// check data in xml file
		$this->assertTablesEqual($expectedTable, $queryTable);
	}
	
	/*
	public function testGetUser() {

		$user 
	}
	 */
}
