<?php

class DALTest extends PHPUnit_Framework_TestCase {
	
	public function testConstruct() {
	
		$dal = new dal\DAL(new dal\StubConnection());
		$this->assertInstanceOf('dal\DAL', $dal);
	}

	public function testRegister() {

		$dal = new dal\DAL(new dal\StubConnection());
		$dal->loadFiles();

		$this->assertEquals(2, $dal->getRegistryCount()); }

	public function testFindOp() {

		$dal =  new dal\DAL(new dal\StubConnection());
		$dal->loadFiles();
		$op = $dal->getCQuery('test', 'con');

		$this->assertInstanceOf('dal\DBQuery', $op);
	}

	public function testWorksAsObject() {

		$dal = new dal\DAL(new dal\StubConnection());
		$dal->loadFiles();
		$op = $dal->test;

		$this->assertEquals('test', $op->getName());
	}

	/**
	 * @expectedException Exception
	 */
	public function testFindOpFail() {

		$dal = new dal\DAL(new dal\StubConnection());
		$dal->loadFiles();
		$op = $dal->getMamieVanDoren;
	}


}

?>
