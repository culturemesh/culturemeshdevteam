<?php

class UserTest extends PHPUnit_Framework_TestCase {

	/**
	 * @covers User::__construct
	 *
	 */
	public function testConstruct() {

		$user = new dobj\User();
		$this->assertInstanceOf('dobj\User', $user);
	}


	/**
	 * @covers User:::username()
	 *
	 */
	public function testUsername() {

		$user = new dobj\User();
		$user->username('Jiminy');

		$this->assertEquals('Jiminy', $user->username);
	}

	public function testUsernameFail() {

		$this->setExpectedException('InvalidArgumentException');
		$user = new dobj\User();
		$user->username(11);
	}

	/**
	 * @covers User:::first_name()
	 *
	 */
	public function testFirstName() {

		$user = new dobj\User();
		$user->first_name('Jiminy');

		$this->assertEquals('Jiminy', $user->first_name);
	}

	public function testFirstNameFail() {

		$this->setExpectedException('InvalidArgumentException');
		$user = new dobj\User();
		$user->first_name(11);
	}

	/**
	 * @covers User:::last_name()
	 *
	 */
	public function testLastName() {

		$user = new dobj\User();
		$user->last_name('Jiminy');

		$this->assertEquals('Jiminy', $user->last_name);
	}

	public function testLastNameFail() {

		$this->setExpectedException('InvalidArgumentException');
		$user = new dobj\User();
		$user->last_name(11);
	}

	/**
	 * @covers User:::email()
	 *
	 */
	public function testEmail() {

		$user = new dobj\User();
		$user->email('Jiminy');

		$this->assertEquals('Jiminy', $user->email);
	}

	public function testEmailFail() {

		$this->setExpectedException('InvalidArgumentException');
		$user = new dobj\User();
		$user->email(11);
	}

	/**
	 * @covers User:::password()
	 *
	 */
	public function testPassword() {

		$user = new dobj\User();
		$user->password('Jiminy');

		$this->assertEquals('Jiminy', $user->password);
	}

	public function testPasswordFail() {

		$this->setExpectedException('InvalidArgumentException');
		$user = new dobj\User();
		$user->password(11);
	}

	/**
	 * @covers User:::role()
	 *
	 */
	public function testRole() {

		$user = new dobj\User();
		$user->role(11);

		$this->assertEquals(11, $user->role);
	}

	public function testRoleFail() {

		$this->setExpectedException('InvalidArgumentException');
		$user = new dobj\User();
		$user->role('Jiminy');
	}

	/**
	 * @covers User:::register_date()
	 *
	 */
	public function testRegisterDate() {

		$user = new dobj\User();
		$user->register_date('1/20/1991');

		$this->assertEquals('1/20/1991', $user->register_date);
	}

	public function testRegisterDateFail() {

		$this->setExpectedException('InvalidArgumentException');
		$user = new dobj\User();
		$user->register_date('Jiminy');
	}

	/**
	 * @covers User:::last_login()
	 *
	 */
	public function testLastLogin() {

		$user = new dobj\User();
		$user->last_login('1/20/1991');

		$this->assertEquals('1/20/1991', $user->last_login);
	}

	public function testLastLoginFail() {

		$this->setExpectedException('InvalidArgumentException');
		$user = new dobj\User();
		$user->last_login('Jiminy');
	}

	/**
	 * @covers User:::gender()
	 *
	 */
	public function testGender() {

		$user = new dobj\User();
		$user->gender('Jiminy');

		$this->assertEquals('Jiminy', $user->gender);
	}

	public function testGenderFail() {

		$this->setExpectedException('InvalidArgumentException');
		$user = new dobj\User();
		$user->gender(11);
	}

	/**
	 * @covers User:::about_me()
	 *
	 */
	public function testAboutMe() {

		$user = new dobj\User();
		$user->about_me('Jiminy');

		$this->assertEquals('Jiminy', $user->about_me);
	}

	public function testAboutMeFail() {

		$this->setExpectedException('InvalidArgumentException');
		$user = new dobj\User();
		$user->about_me(11);
	}

	/**
	 * @covers User:::events_upcoming()
	 *
	 */
	public function testEventsUpcoming() {

		$user = new dobj\User();
		$user->events_upcoming(11);

		$this->assertEquals(11, $user->events_upcoming);
	}

	public function testEventsUpcomingFail() {

		$this->setExpectedException('InvalidArgumentException');
		$user = new dobj\User();
		$user->events_upcoming('Jiminy');
	}

	/**
	 * @covers User:::events_interested_in()
	 *
	 */
	public function testEventsInterestedIn() {

		$user = new dobj\User();
		$user->events_interested_in(11);

		$this->assertEquals(11, $user->events_interested_in);
	}

	public function testEventsInterestedInFail() {

		$this->setExpectedException('InvalidArgumentException');
		$user = new dobj\User();
		$user->events_interested_in('Jiminy');
	}

	/**
	 * @covers User:::company_news()
	 *
	 */
	public function testCompanyNews() {

		$user = new dobj\User();
		$user->company_news(11);

		$this->assertEquals(11, $user->company_news);
	}

	public function testCompanyNewsFail() {

		$this->setExpectedException('InvalidArgumentException');
		$user = new dobj\User();
		$user->company_news('Jiminy');
	}

	/**
	 * @covers User:::network_activity()
	 *
	 */
	public function testNetworkActivity() {

		$user = new dobj\User();
		$user->network_activity(11);

		$this->assertEquals(11, $user->network_activity);
	}

	public function testNetworkActivityFail() {

		$this->setExpectedException('InvalidArgumentException');
		$user = new dobj\User();
		$user->network_activity('Jiminy');
	}

	/**
	 * @covers User:::confirmed()
	 *
	 */
	public function testConfirmed() {

		$user = new dobj\User();
		$user->confirmed(11);

		$this->assertEquals(11, $user->confirmed);
	}

	public function testConfirmedFail() {

		$this->setExpectedException('InvalidArgumentException');
		$user = new dobj\User();
		$user->confirmed('Jiminy');
	}

	/**
	 * @covers User:::act_code()
	 *
	 */
	public function testActCode() {

		$user = new dobj\User();
		$user->act_code('Jiminy');

		$this->assertEquals('Jiminy', $user->act_code);
	}

	public function testActCodeFail() {

		$this->setExpectedException('InvalidArgumentException');
		$user = new dobj\User();
		$user->act_code(11);
	}

	/**
	 * @covers User:::img_link()
	 *
	 */
	public function testImgLink() {

		$user = new dobj\User();
		$user->img_link('Jiminy');

		$this->assertEquals('Jiminy', $user->img_link);
	}

	public function testImgLinkFail() {

		$this->setExpectedException('InvalidArgumentException');
		$user = new dobj\User();
		$user->img_link(11);
	}
}
