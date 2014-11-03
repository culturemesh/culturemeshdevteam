<?php

/**
 * @covers Environment::__construct
 */
class EnvironmentTest extends PHPUnit_Framework_TestCase
{
	protected $ev;

	public static function setUpBeforeClass() {
		include 'Environment.php';
	}

	protected function setUp() {
		$this->ev = new Environment();
	}

	protected function tearDown() {
		$this->ev = NULL;
	}

	public function testHasDomainUrl() {

		$this->assertObjectHasAttribute('domain_url', $this->ev);
	}

	public function testHasShortDomainUrl() {

		$this->assertObjectHasAttribute('short_domain_url', $this->ev);
	}

	public function testHasDomainName() {

		$this->assertObjectHasAttribute('domain_name', $this->ev);
	}

	public function testHasFacebookUrl() {

		$this->assertObjectHasAttribute('facebook_url', $this->ev);
	}

	public function testHasTwitterUrl() {

		$this->assertObjectHasAttribute('twitter_url', $this->ev);
	}

	public function testHasSupportEmail() {

		$this->assertObjectHasAttribute('support_email', $this->ev);
	}

	public function testHasImageDirectory() {

		$this->assertObjectHasAttribute('img_dir', $this->ev);
	}

	public function testHasBlankImage() {

		$this->assertObjectHasAttribute('blank_img', $this->ev);
	}

	public function testDbServer() {

		$this->assertObjectHasAttribute('db_server', $this->ev);
	}

	public function testDbUser() {

		$this->assertObjectHasAttribute('db_user', $this->ev);
	}

	public function testDbPass() {

		$this->assertObjectHasAttribute('db_pass', $this->ev);
	}

	public function testDbName() {

		$this->assertObjectHasAttribute('db_name', $this->ev);
	}

	public function testAutoloadLib() {

		$dobj = new dobj\User();
		$this->assertInstanceOf('dobj\User', $dobj);
	}

	public function testAutoloadRoot() {

		$foo = new Foo();
		$this->assertInstanceOf('Foo', $foo);
	}
}

?>
