<?php

class ImageTest extends PHPUnit_Framework_TestCase {

	public function testConstruct() {

		global $cm;
		$img = new dobj\Image($cm);
		$this->assertInstanceOf('dobj\Image', $img);

	}

	public function testGetRealUrl() {

		global $cm;
		$img = new dobj\Image($cm);
		$url = $img->realUrl;
	}

	public function testGetHostUrl() {

		global $cm;
		$img = new dobj\Image($cm);
		$url = $img->hostUrl();
	}
}
