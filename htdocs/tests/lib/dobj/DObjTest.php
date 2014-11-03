<?php

class DObjTest extends PHPUnit_Framework_TestCase {

	public function testConstruct() {
		$dobj = new dobj\DObj();

		$this->assertInstanceOf("dobj\DObj", $dobj);
	}

	public function testGetSetMagic() {

		$dobj = new dobj\DObj();
		$dobj->id = 1;

		$this->assertEquals(1, $dobj->id);
	}

	public function testJsId() {

		$dobj = new dobj\DObj();
		$dobj->id(1);

		$this->assertEquals(1, $dobj->id);
	}

	public function testJsIdFail() {

		$this->setExpectedException('InvalidArgumentException');
		$dobj = new dobj\DObj();
		$dobj->id('string');
	}
}

?>
