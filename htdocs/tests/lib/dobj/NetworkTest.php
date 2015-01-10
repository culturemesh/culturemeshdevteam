<?php

class NetworkTest extends PHPUnit_Framework_TestCase {

	protected $network;

	public function setUp() {
		$this->network = new dobj\Network();
	}

	public function tearDown() {

	}

	public function testConstruct() {

		$network = new dobj\Network();
		$this->assertInstanceOf('dobj\Network', $network);
	}

	// cc, cc
	public function testGetNetworkTitle_1() {

		$this->network->origin = new dobj\City();
		$this->network->location = new dobj\City();

		$this->network->origin->name = 'City';
		$this->network->origin->region_name = 'Region';
		$this->network->origin->country_name = 'Country';
		$this->network->location->name = 'City';
		$this->network->location->region_name = 'Region';
		$this->network->location->country_name = 'Country';

		$title = $this->network->getTitle();
		$this->assertEquals($title, 'From City, Region, Country in City, Region, Country');
	}

	// cc, cc - alt
	public function testGetNetworkTitle_1alt() {

		$this->network->origin = new dobj\City();
		$this->network->location = new dobj\City();

		$this->network->origin->name = 'City';
		$this->network->origin->country_name = 'Country';
		$this->network->location->name = 'City';
		$this->network->location->country_name = 'Country';

		$title = $this->network->getTitle();
		$this->assertEquals($title, 'From City, Country in City, Country');
	}

	// rc, cc
	public function testGetNetworkTitle_2() {

		$this->network->origin = new dobj\Region();
		$this->network->location = new dobj\City();

		$this->network->origin->name = 'Region';
		$this->network->origin->country_name = 'Country';
		$this->network->location->name = 'City';
		$this->network->location->region_name = 'Region';
		$this->network->location->country_name = 'Country';

		$title = $this->network->getTitle();
		$this->assertEquals($title, 'From Region, Country in City, Region, Country');
	}

	// co, cc	
	public function testGetNetworkTitle_3() {

		$this->network->origin = new dobj\Country();
		$this->network->location = new dobj\City();

		$this->network->origin->name = 'Country';
		$this->network->location->name = 'City';
		$this->network->location->region_name = 'Region';
		$this->network->location->country_name = 'Country';
		$title = $this->network->getTitle();

		$this->assertEquals($title, 'From Country in City, Region, Country');
	}

	// _l, cc
	public function testGetNetworkTitle_4() {

		$this->network->origin = new dobj\Language();
		$this->network->location = new dobj\City();

		$this->network->origin->name = 'Language';
		$this->network->location->name = 'City';
		$this->network->location->region_name = 'Region';
		$this->network->location->country_name = 'Country';
		$title = $this->network->getTitle();

		$title = $this->network->getTitle();
		$this->assertEquals($title, 'Language speakers in City, Region, Country');
	}

	// cc, rc
	public function testGetNetworkTitle_5() {

		$this->network->origin = new dobj\City();
		$this->network->location = new dobj\Region();

		$this->network->origin->name = 'City';
		$this->network->origin->region_name = 'Region';
		$this->network->origin->country_name = 'Country';
		$this->network->location->name = 'Region';
		$this->network->location->country_name = 'Country';

		$title = $this->network->getTitle();
		$this->assertEquals($title, 'From City, Region, Country in Region, Country');
	}

	// rc, rc
	public function testGetNetworkTitle_6() {

		$this->network->origin = new dobj\Region();
		$this->network->location = new dobj\Region();

		$this->network->origin->name = 'Region';
		$this->network->origin->country_name = 'Country';
		$this->network->location->name = 'Region';
		$this->network->location->country_name = 'Country';

		$title = $this->network->getTitle();
		$this->assertEquals($title, 'From Region, Country in Region, Country');
	}

	// co, rc
	public function testGetNetworkTitle_7() {

		$this->network->origin = new dobj\Country();
		$this->network->location = new dobj\Region();

		$this->network->origin->name = 'Country';
		$this->network->location->name = 'Region';
		$this->network->location->country_name = 'Country';

		$title = $this->network->getTitle();
		$this->assertEquals($title, 'From Country in Region, Country');
	}

	// _l, rc
	public function testGetNetworkTitle_8() {

		$this->network->origin = new dobj\Language();
		$this->network->location = new dobj\Region();

		$this->network->origin->name = 'Language';
		$this->network->location->name = 'Region';
		$this->network->location->country_name = 'Country';

		$title = $this->network->getTitle();
		$this->assertEquals($title, 'Language speakers in Region, Country');
	}

	// cc, co 
	public function testGetNetworkTitle_9() {

		$this->network->origin = new dobj\City();
		$this->network->location = new dobj\Country();

		$this->network->origin->name = 'City';
		$this->network->origin->region_name = 'Region';
		$this->network->origin->country_name = 'Country';
		$this->network->location->name = 'Country';

		$title = $this->network->getTitle();
		$this->assertEquals($title, 'From City, Region, Country in Country');
	}

	// rc, co 
	public function testGetNetworkTitle_10() {

		$this->network->origin = new dobj\Region();
		$this->network->location = new dobj\Country();

		$this->network->origin->name = 'Region';
		$this->network->origin->country_name = 'Country';
		$this->network->location->name = 'Country';

		$title = $this->network->getTitle();
		$this->assertEquals($title, 'From Region, Country in Country');
	}

	// co, co 
	public function testGetNetworkTitle_11() {

		$this->network->origin = new dobj\Country();
		$this->network->location = new dobj\Country();

		$this->network->origin->name = 'Country';
		$this->network->location->name = 'Country';

		$title = $this->network->getTitle();
		$this->assertEquals($title, 'From Country in Country');
	}
	
	// _l, co 
	public function testGetNetworkTitle_12() {

		$this->network->origin = new dobj\Language();
		$this->network->location = new dobj\Country();

		$this->network->origin->name = 'Language';
		$this->network->location->name = 'Country';

		$title = $this->network->getTitle();
		$this->assertEquals($title, 'Language speakers in Country');
	}
}

?>
