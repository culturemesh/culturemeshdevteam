<?php

class ImageUploadTest extends PHPUnit_Framework_TestCase {

	public function testConstruct() {

		$dir = 'test';
		$postname = 'test';

		$dal = new dal\DAL(new dal\StubConnection());
	
		$iu = new misc\ImageUpload(array(
				'dir' => $dir,
				'postname' => $postname,
				'validation_type' => array(),
				'validation_size' => '1M',
				'thumbnail' => array(
					'thumbnail' => true,
					'size' => '75', // in pixels
					'suffix' => 'thumb')
				)
			);

		$this->assertInstanceOf('misc\ImageUpload', $iu);
	}

	/*
	public function testConvertToDir() {

		$iu = new misc\ImageUpload(array());
		$hash = '1111111111111111111111111';

		$newthing = $iu->convertToDir($hash);
		$this->assertEquals('11/1111/1111/1111/1111/1111/111', $newthing);
	}
	 */
}

?>
