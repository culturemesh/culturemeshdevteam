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

		$this->language_network = new dobj\Network();
		$this->language_network->city_cur = 'Detroit';
		$this->language_network->region_cur = 'Michigan';
		$this->language_network->country_cur = 'United States';
		$this->language_network->language_origin = 'Chinese';
		$this->language_network->network_class = '_l';
	}

	public function testConstruct() {

		$query = new api\TwitterQuery();

		$this->assertEquals('api\TwitterQuery', get_class($query));
	}

	/*
	 * Location Tests
	 *
	 * Sample Network: From China in Detroit, Michigan
	 *
	 * %20 - <space>
	 * %23 - #
	 */
	public function testLocationNetworkBuildSearch() {

		$query = new api\TwitterQuery();
		$query->buildSearch($this->location_network);

		$this->assertEquals('https://api.twitter.com/1.1/search/tweets.json?q=Detroit%20OR%20China', $query->getSearch());
	}

	public function testLanguageNetworkBuildSearch() {

		$query = new api\TwitterQuery();
		$query->buildSearch($this->language_network);

		$this->assertEquals('https://api.twitter.com/1.1/search/tweets.json?q=Detroit%20OR%20lang:zh-tw', $query->getSearch());
	}

	public function testHashNetworkBuildSearch() {

		$this->location_network = new dobj\Network();
		$this->location_network->city_cur = 'Grand Rapids';
		$this->location_network->region_cur = 'Michigan';
		$this->location_network->country_cur = 'United States';
		$this->location_network->country_origin = 'China';
		$this->location_network->network_class = 'co';

		$query = new api\TwitterQuery();
		$query->buildSearch($this->location_network);

		$this->assertEquals('https://api.twitter.com/1.1/search/tweets.json?q=Grand%20Rapids%20OR%20%23GrandRapids%20OR%20China', $query->getSearch());
	}
}

?>
