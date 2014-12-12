<?php

class ImageTest extends PHPUnit_Framework_TestCase {

	public function testConstruct() {

		global $cm;
		$img = new dobj\Image($cm);
		$this->assertInstanceOf('dobj\Image', $img);

	}

	public function testGetRealUrl() {

		$this->markTestSkipped('Worrying about images later');

		global $cm;
		$img = new dobj\Image($cm);
		$url = $img->realUrl;
	}

	public function testGetHostUrl() {

		$this->markTestSkipped('Worrying about images later');

		global $cm;
		$img = new dobj\Image($cm);
		$url = $img->hostUrl;
	}
}
