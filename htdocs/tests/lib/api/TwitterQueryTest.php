<?php

class TwitterQueryTest extends PHPUnit_Framework_TestCase {

	protected $location_network;
	protected $language_network;

	public function setUp() {

		$this->location_network = new dobj\Network();
		$this->location_network->city_cur = 'Detroit';
		$this->location_network->region_cur = 'Michigan';
		$this->location_network->country_cur = 'United States';
		$this->location_network->country_origin = 'China';
		$this->location_network->network_class = 'co';

		// set levels
		$this->location_network->query_level = 2;
		$this->location_network->query_origin_scope = 1;
		$this->location_network->query_location_scope = 1;
		$this->location_network->query_since_date = '2010-01-01';

		$this->language_network = new dobj\Network();
		$this->language_network->city_cur = 'Detroit';
		$this->language_network->region_cur = 'Michigan';
		$this->language_network->country_cur = 'United States';
		$this->language_network->language_origin = 'Chinese';
		$this->language_network->network_class = '_l';

		// set levels
		$this->language_network->query_level = 2;
		$this->language_network->query_origin_scope = 1;
		$this->language_network->query_location_scope = 1;
		$this->language_network->query_since_date = '2010-01-01';
	}

	public function testConstruct() {

		$query = new api\TwitterQuery($this->location_network);

		$this->assertEquals('api\TwitterQuery', get_class($query));
	}

	/*
	 * Location Tests
	 *
	 * Sample Network: From China in Detroit, Michigan
	 *
	 * %20 - <space>
	 * %23 - #
	 * %28 - (
	 * %29 - )
	 */
	public function testLocationNetworkBuildSearch() {

		$query = new api\TwitterQuery($this->location_network);

		$this->assertEquals(urldecode('https://api.twitter.com/1.1/search/tweets.json?q=(China) (#UnitedStates OR "United States") -filter:retweets&result_type=mixed&since=2010-01-01'), urldecode($query->getSearch()));
	}

	public function testLanguageNetworkBuildSearch() {

		$query = new api\TwitterQuery($this->language_network);

		$this->assertEquals(urldecode('https://api.twitter.com/1.1/search/tweets.json?q=(#UnitedStates OR "United States") -filter:retweets&lang=zh-tw&result_type=mixed&since=2010-01-01'), urldecode($query->getSearch()));
	}

	/*
	 * Tests to make sure a proper hashtag is built
	 * for locations with whitespace in the middle
	 *
	 */
	public function testHashNetworkBuildSearch() {

		$this->location_network->city_cur = 'Grand Rapids';
		$this->location_network->region_cur = 'Michigan';
		$this->location_network->country_cur = 'United States';
		$this->location_network->country_origin = 'China';
		$this->location_network->network_class = 'co';

		$this->location_network->query_location_scope = 3;

		$query = new api\TwitterQuery($this->location_network);

		$this->assertEquals(urldecode('https://api.twitter.com/1.1/search/tweets.json?q=(China) (%23GrandRapids%20OR%20%22Grand%20Rapids%22) -filter:retweets&result_type=mixed&since=2010-01-01'), urldecode( $query->getSearch() ));
	}

	public function testSlashLanguageNetworkBuildSearch() {

		$this->language_network->language_origin = 'Mandarin Chinese/Putonghua';
		$this->language_network->query_location_scope = 3;

		$query = new api\TwitterQuery($this->language_network);

		$this->assertEquals(urldecode('https://api.twitter.com/1.1/search/tweets.json?q=(%23MandarinChinese%20OR%20%22Mandarin%20Chinese%22%20OR%20Putonghua) (Detroit) -filter:retweets&lang=zh-tw&result_type=mixed&since=2010-01-01'), urldecode($query->getSearch()));
	}

	public function testParenthesisLanguageNetworkBuildSearch() {

		$this->language_network->language_origin = 'American Sign Language (ASL)';

		$query = new api\TwitterQuery($this->language_network);

		$this->assertEquals(urldecode('https://api.twitter.com/1.1/search/tweets.json?q=(%23ASL%20OR%20%22American%20Sign%20Language%22)%20(#UnitedStates OR "United States") -filter:retweets&result_type=mixed&since=2010-01-01'), urldecode($query->getSearch()));
	}
}

?>
