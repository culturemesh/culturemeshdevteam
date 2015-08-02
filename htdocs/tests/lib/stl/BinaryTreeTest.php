<?php

class BinaryTreeTest extends PHPUnit_Framework_TestCase {

	public function testConstruct() {

		$tree = new stl\BinaryTree();
		$this->assertInstanceOf('stl\BinaryTree', $tree);
	}

	public function testInsert() {

		$tree = new stl\BinaryTree();

		$test_value = 1;
		$tree->insert( $test_value );

		$this->assertTrue($tree->find($test_value));
	}

	public function testInsertMultipl() {

		$tree = new stl\BinaryTree();

		$tree->insert( 1);
		$tree->insert( 12);
		$tree->insert(8);

		$this->assertTrue($tree->find(8));
		$this->assertTrue($tree->find(12));
		$this->assertTrue($tree->find(1));
	}
}

?>
