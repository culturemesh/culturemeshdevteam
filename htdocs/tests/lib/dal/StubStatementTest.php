<?php

class StubStatementTest extends PHPUnit_Framework_TestCase {

	public function testConstruct() {

		$ss = new dal\StubStatement('query shuggamupp');
		$this->assertInstanceOf('dal\StubStatement', $ss);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testConstructFail() {

		$ss = new dal\StubStatement(3.1);
	}

	public function testBindParameters() {

		$ss = new dal\StubStatement('query shuggamupp ? ?');
		$query = $ss->bind_param('ss', 'south', 'park');

		$this->assertEquals('query shuggamupp south park', $query);
	}

	/**
	 * @expectedException Exception
	 */
	public function testBindParametersFail() {

		$ss = new dal\StubStatement('query shuggamupp ? ?');
		$query = $ss->bind_param('ss', 'south', 'park', 'loser');
	}
}

?>
