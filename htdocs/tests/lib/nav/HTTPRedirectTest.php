<?php

class HTTPRedirectTest extends PHPUnit_Framework_TestCase {

	protected $pages;

	public function setUp() {

		$this->pages = array('index', 'network', 'search_results', 
			'careers', 'about', 'press');
	}

	public function testConstruct() {

		$hp = new nav\HTTPRedirect('http://www.culturemesh.com/', $this->pages);
		$this->assertInstanceOf('nav\HTTPRedirect', $hp);
	}

	public function testGetPath() {

		// index
		$hp = new nav\HTTPRedirect('http://www.culturemesh.com/', $this->pages);
		$this->assertEquals('index.php', $hp->getPath());

		// control/match
		$hp = new nav\HTTPRedirect('http://www.culturemesh.com/network/123', $this->pages);
		$this->assertEquals('/', $hp->getPath());
	}

	public function testGetControl() {

		// control/match
		$hp = new nav\HTTPRedirect('http://www.culturemesh.com/network/123', $this->pages);

		$this->assertEquals($hp->getControl(), array(
			'control' => 'network',
			'value' => 123));
	}

	public function testGetUrl() {

		// control/match
		$hp = new nav\HTTPRedirect('http://www.culturemesh.com/network/123', $this->pages);
		$this->assertEquals('/network/123', $hp->getUrl());
	}
}

?>
