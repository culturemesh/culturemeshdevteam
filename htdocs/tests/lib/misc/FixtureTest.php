<?php

class FixtureTest extends PHPUnit_Framework_TestCase {

	/**
	 * @covers __construct 
	 */
	public function testConstruct() {

		// create fixture obj
		$fs = new misc\TestFixture(array(
			'fixture' => array('file')
			));

		// assert proper object
		$this->assertInstanceOf('misc\TestFixture', $fs);

		$this->assertEquals('cmdata-', $fs->prefix);
		$this->assertEquals('.xml', $fs->suffix);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testConstructFail1() {

		$fs = new misc\TestFixture(array(1, 2, 3));	
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testConstructFail2() {

		$fs = new misc\TestFixture(array(
			"1" => "value"));
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testConstructFail3() {


		$fs = new misc\TestFixture(array(
			"1" => array("value")));
	}


	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testConstructFail4() {

		$fs = new misc\TestFixture();	
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testGetFail1() {

		$fs = new misc\TestFixture(array(
			"key" => array("value")));
		$fs->fixtures;
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testGetFail2() {

		$fs = new misc\TestFixture(array(
			"key" => array("value")));
		$fs->somethingNotThere;
	}

	public function testGet() {

		$fs = new misc\TestFixture(array(
			"key" => array("value")));

		$cubby = $fs->prefix;
		$this->assertEquals('cmdata-', $cubby);

		$cubby = $fs->suffix;
		$this->assertEquals('.xml', $cubby);
	}

	public function testGetFilenames1() {

		$fs = new misc\TestFixture(array(
			"key" => array("value")));

		$files = $fs->getFilenames();

		$this->assertTrue(is_array($files));
		$this->assertCount(1, $files);

		// obscured, but should be: 
		// ** /key/cmdata-value.xml **
		$this->assertEquals(DIRECTORY_SEPARATOR.'key'.DIRECTORY_SEPARATOR.$fs->prefix.'value'.$fs->suffix, $files[0]);
	}

	public function testGetFilenames2() {

		$fixture = array(
			"key" => array("value", "second"),
			"key2" => array("salt")
		);

		$fs = new misc\TestFixture($fixture);

		$files = $fs->getFilenames();

		$this->assertTrue(is_array($files));
		$this->assertCount(3, $files);

		// obscured, but should be: 
		// ** /key/cmdata-value.xml **
		$this->assertEquals(DIRECTORY_SEPARATOR.'key'.DIRECTORY_SEPARATOR.$fs->prefix.'value'.$fs->suffix, $files[0]);
		$this->assertEquals(DIRECTORY_SEPARATOR.'key'.DIRECTORY_SEPARATOR.$fs->prefix.'second'.$fs->suffix, $files[1]);
		$this->assertEquals(DIRECTORY_SEPARATOR.'key2'.DIRECTORY_SEPARATOR.$fs->prefix.'salt'.$fs->suffix, $files[2]);
	}
}

?>
