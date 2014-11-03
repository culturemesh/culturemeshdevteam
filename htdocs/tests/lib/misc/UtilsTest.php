<?php

class UtilTest extends PHPUnit_Framework_TestCase {

	public function testHasStringKey() {

		// you know
		$empty_array = array();

		// numeric key
		$strnum_key = array(
			'0' => 'value');

		// no key
		$nokeys = array('Pacific Overtures', 'Sunday in the Park with George');

		// str key
		$str_key = array(
			'key' => 'value');

		// str key
		$mixed_key = array(
			'0' => 'value',
			'key' => 'value');

		$this->assertFalse(misc\Util::hasStringKey($empty_array));
		$this->assertFalse(misc\Util::hasStringKey($strnum_key));
		$this->assertFalse(misc\Util::hasStringKey($nokeys));
		$this->assertTrue(misc\Util::hasStringKey($str_key));
		$this->assertTrue(misc\Util::hasStringKey($mixed_key));
	}
}

?>
