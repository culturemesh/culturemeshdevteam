<?php

class UserTest extends PHPUnit_Framework_TestCase {

	/**
	 * @covers User::__construct
	 *
	 */
	public function testConstruct() {

		$user = new dobj\User();
		$this->assertInstanceOf('dobj\User', $user);
	}
}
