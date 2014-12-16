<?php

class PostTest extends PHPUnit_Framework_TestCase {

	/**
	 * @covers User::__construct
	 *
	 */
	public function testConstruct() {

		$post = new dobj\Post();
		$this->assertInstanceOf('dobj\Post', $post);
	}
}

?>
