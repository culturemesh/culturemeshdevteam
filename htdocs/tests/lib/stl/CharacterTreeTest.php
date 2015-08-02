<?php

class CharacterTreeTest extends PHPUnit_Framework_TestCase {

	public function testConstruct() {

		$tree = new stl\CharacterTree();
		$this->assertInstanceOf('stl\CharacterTree', $tree);
	}

	public function testInsertSingleString() {

		$tree = new stl\CharacterTree();

		$test_string = 'single';
		$tree->insert( $test_string );

		$this->assertTrue($tree->find($test_string));
	}

	public function testInsertMultipleStrings() {

		$tree = new stl\CharacterTree();

		$test_string_1 = 'single';
		$test_string_2 = 'double';
		$tree->insert( $test_string_1 );
		$tree->insert( $test_string_2 );

		$this->assertTrue($tree->find($test_string_1));
		$this->assertTrue($tree->find($test_string_2));
	}

	public function testShorterVersion() {

		$tree = new stl\CharacterTree();

		$long_string = 'bugaboo';
		$short_string = 'bug';
		$tree->insert($long_string);
		$this->assertTrue( $tree->insert($short_string));
		$this->assertTrue( $tree->find($short_string) );
	}

	public function testStringNotFound() {

		$tree = new stl\CharacterTree();

		$tree->insert('Think of it as thrift');
		$this->assertFalse( $tree->find('Such a nice, plump frame, what\'s his name has'));
	}

		/*
	public function testLongString() {

		$tree = new stl\CharacterTree();

		$tree->insert(' In case humanity ever has to leave the surface and live underground, I
			wonder what the floors will be called. Will they work like buildings
			do now. What will we call today\'s buildings? Classic buildings?
			Toppies? What will we call the first flooor? We can\'t calll it the
			ground floor. They\'ll all be ground floors! The sky floor, maybe?  If
			we\'re really trippy, we could call it the ceiling floor.');

		$this->assertTrue( true);
	}
		 */
}

?>
