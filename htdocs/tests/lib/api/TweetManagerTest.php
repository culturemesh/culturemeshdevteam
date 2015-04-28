<?php

class TweetManagerTest extends PHPUnit_Framework_TestCase {

	protected $network;

	public function setUp() {

		$this->network = new dobj\Network();
	}

	public function testConstruct() {

		global $cm;

		$tweet_manager = new api\TweetManager($cm, $this->network);

		$this->assertInstanceOf('api\TweetManager', $tweet_manager);
	}
}

?>
