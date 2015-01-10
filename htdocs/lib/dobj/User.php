<?php
namespace dobj;

class User extends DObj {

	protected $username;	// varchar(30)
	protected $first_name;	// varchar(30)
	protected $last_name;	// varchar(30)
	protected $email;		// varchar(30)
	protected $password;	// varchar(30)
	protected $role;		// smallint(1)
	protected $register_date;	// timestamp
	protected $last_login;	// timestamp
	protected $gender;		// varchar(1)
	protected $about_me;
	protected $events_upcoming;
	protected $events_interested_in;
	protected $company_news;
	protected $network_activity;
	protected $confirmed;
	protected $act_code;
	protected $img_link;

	// events
	protected $yn_events;
	protected $ya_events;
	protected $yh_events;

	// posts
	protected $yp_posts;

	// networks
	protected $yp_networks;
	protected $ye_networks;
	protected $yn_networks;

	/*
	 * Abstract Methods
	 *
	 */
	public static function createFromId($id, $dal, $do2db) {

		$user = new User();
		$user->id = $id;

		return $do2db->execute($dal, $user, 'getUserById');
	}

	/**
	 * DATABASE METHODS
	 *
	 */
	public static function testQuery($id, $dal, $do2db) {

		$user = new User();
		$user->id = 1;

		return $do2db->execute($dal, $user, 'getUserTest');
	}

	public function getEventsInYourNetworks($dal, $do2db) {

		// set query object
		$obj = new Blank();
		$obj->id_user = $this->id;

		// query
		$this->yn_events = $do2db->execute($dal, $obj, 'getEventsInYourNetworks');

		if (get_class($this->yn_events) == 'PDOStatement') {
			$err = $this->yn_events->errorInfo();
			print_r($err);
			$this->yn_events = NULL;
		}
	}

	public function getEventsHosting($dal, $do2db) {

		// set query object
		$obj = new Blank();
		$obj->id_host = $this->id;

		// query
		$this->yh_events = $do2db->execute($dal, $obj, 'getEventsByUserId');

		if (get_class($this->yh_events) == 'PDOStatement') {
			$err = $this->yh_events->errorInfo();
			print_r($err);
			$this->yh_events = NULL;
		}
	}

	public function getEventsAttending($dal, $do2db) {

		// set query object
		$obj = new Blank();
		$obj->id_guest = $this->id;

		// query
		$this->ya_events = $do2db->execute($dal, $obj, 'getEventRegistrationsByUserId');

		if (get_class($this->ya_events) == 'PDOStatement') {
			$err = $this->ya_events->errorInfo();
			print_r($err);
			$this->ya_events = NULL;
		}
	}

	public function getPosts($dal, $do2db) {

		$obj = new Blank();
		$obj->id_user = $this->id;
		$obj->lbound = 0;
		$obj->ubound = 10;

		$this->yp_posts = $do2db->execute($dal, $obj, 'getPostsByUserId');

		if (get_class($this->yp_posts) == 'PDOStatement')
			$this->yp_posts = NULL;
	}

	public function getNetworksWithPosts($dal, $do2db) {

		$obj = new Blank();

		if ($this->yp_posts == NULL) 
			return False;

		$obj->idlist = $this->getNids($this->yp_posts);

		$this->yp_networks = $do2db->execute($dal, $obj, 'getNetworksByIds');
	}

	public function getNetworksWithEvents($dal, $do2db) {

		$obj = new Blank();

		if ($this->ya_events == NULL) 
			return False;

		$obj->idlist = $this->getNids($this->ya_events);

		$this->ye_networks = $do2db->execute($dal, $obj, 'getNetworksByIds');
	}

	public function getMemberNetworks($dal, $do2db) {

		$obj = new Blank();
		$obj->id_user = $this->id;

		$this->yn_networks = $do2db->execute($dal, $obj, 'getNetworksByUserId');
	}

	private function getNids($array) {

		// add ids into friendly mysql variable
		$inlist = '(';
		
		for ($i = 0; $i < count($array); $i++) {
			// add item
			$inlist .= $array[$i]->id_network;

			// add comma
			if (count($array) - $i > 1) {
				$inlist .= ', ';
			}
		}

		// add end parenthesis
		$inlist .= ')';

		return $inlist;
	}
}

?>
