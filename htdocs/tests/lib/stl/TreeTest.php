<?php

class TreeTest extends PHPUnit_Framework_TestCase {

	public function testConstruct() {

		$tree = new stl\Tree();
		$this->assertInstanceOf('stl\Tree', $tree);
	}
}

?>
