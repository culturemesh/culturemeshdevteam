<?php

class RemoraTest extends PHPUnit_Framework_TestCase {

	protected $dobj;

	public function setUp() {

		$this->blank = new dobj\Blank();
	}

	public function testConstruct() {

		$remora = new dal\Remora();

		$this->assertInstanceOf('\dal\Remora', $remora);
	}

	public function testSetFunction() {

		$remora = new dal\Remora();
		$remora->setFunction(function() {
			return True;
		});

		$this->assertEquals('Closure', get_class($remora->function[0]));
	}

	public function testEverything() {

		$remora = new dal\Remora();
		$remora->id_count = 0;
		$remora->setFunction(function($obj) {

			$this->id_count += $obj->id;
		});

		$row = array('id' => 1);
		$obj = \dobj\Blank::createFromDataRow($row, $remora);

		$this->assertEquals($remora->id_count, 1);
	}
}

?>
