<?php

class LanguageTest extends PHPUnit_Framework_TestCase {

	protected $lang;

	public function setUp() {

		$this->lang = new dobj\Language();
	}

	public function testConstruct() {
		
		$this->assertInstanceOf('dobj\Language', $this->lang);
	}

	public function testToString() {

		$this->assertEquals(null, $this->lang->toString());
	}
}

?>
