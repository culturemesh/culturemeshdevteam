<?php

include 'HelloClass.php';

/**
 * @covers HelloClass::__construct
 * @covers HelloClass::getHello
 */
class HelloClassTest extends PHPUnit_Framework_TestCase 
{
	public function testConstructor() {
		$h = new HelloClass('Hello world');

		$this->assertEquals('Hello world', $h->getHello());
	}

}
?>
