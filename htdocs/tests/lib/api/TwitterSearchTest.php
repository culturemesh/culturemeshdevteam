<?php

class TwitterSearchTest extends PHPUnit_Framework_TestCase {

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
}
