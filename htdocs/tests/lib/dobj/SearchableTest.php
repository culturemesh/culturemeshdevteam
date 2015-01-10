<?php

class SearchableTest extends PHPUnit_Framework_TestCase {

	protected $srch;

	public function setUp() {
		$this->srch = new dobj\Searchable();
	}

	public function tearDown() {

	}

	public function testConstruct() {
		$this->assertInstanceOf('dobj\Searchable', $this->srch);
	}
}

?>
