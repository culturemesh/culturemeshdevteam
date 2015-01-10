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

	/*
	public static function createFromDataRow($row) {

	}
	 */

	/*
	 * Accessors and Mutators
	 *
	 */
	public function username($value=NULL) {
		
		if ($value == NULL)
			return $this->username;

		if (!is_string($value)) {
			throw new \InvalidArgumentException('Id must be a string');
			return false;
		}

		$this->username = $value;

		return true;
	}

	public function first_name($value=NULL) {
		
		if ($value == NULL)
			return $this->first_name;

		if (!is_string($value)) {
			throw new \InvalidArgumentException('Id must be a string');
			return false;
		}

		$this->first_name = $value;

		return true;
	}


	public function last_name($value=NULL) {
		
		if ($value == NULL)
			return $this->last_name;

		if (!is_string($value)) {
			throw new \InvalidArgumentException('Id must be a string');
			return false;
		}

		$this->last_name = $value;

		return true;
	}

	public function email($value=NULL) {
		
		if ($value == NULL)
			return $this->email;

		if (!is_string($value)) {
			throw new \InvalidArgumentException('Id must be a string');
			return false;
		}

		$this->email = $value;

		return true;
	}

	public function password($value=NULL) {
		
		if ($value == NULL)
			return $this->password;

		if (!is_string($value)) {
			throw new \InvalidArgumentException('Id must be a string');
			return false;
		}

		$this->password = $value;

		return true;
	}

	public function role($value=NULL) {
		
		if ($value == NULL)
			return $this->role;

		if (!is_int($value)) {
			throw new \InvalidArgumentException('Id must be an integer');
			return false;
		}

		$this->role = $value;

		return true;
	}


	public function register_date($value=NULL) {
		
		if ($value == NULL)
			return $this->register_date;

		if (!strtotime($value)) {
			throw new \InvalidArgumentException('Id must be a string');
			return false;
		}

		$this->register_date = $value;

		return true;
	}

	public function last_login($value=NULL) {
		
		if ($value == NULL)
			return $this->last_login;

		if (!strtotime($value)) {
			throw new \InvalidArgumentException('Id must be a string');
			return false;
		}

		$this->last_login = $value;

		return true;
	}

	public function gender($value=NULL) {
		
		if ($value == NULL)
			return $this->gender;

		if (!is_string($value)) {
			throw new \InvalidArgumentException('Id must be a string');
			return false;
		}

		$this->gender = $value;

		return true;
	}

	public function about_me($value=NULL) {
		
		if ($value == NULL)
			return $this->about_me;

		if (!is_string($value)) {
			throw new \InvalidArgumentException('Id must be a string');
			return false;
		}

		$this->about_me = $value;

		return true;
	}

	public function events_upcoming($value=NULL) {
		
		if ($value == NULL)
			return $this->events_upcoming;

		if (!is_int($value)) {
			throw new \InvalidArgumentException('Id must be an integer');
			return false;
		}

		$this->events_upcoming = $value;

		return true;
	}

	public function events_interested_in($value=NULL) {
		
		if ($value == NULL)
			return $this->events_interested_in;

		if (!is_int($value)) {
			throw new \InvalidArgumentException('Id must be an integer');
			return false;
		}

		$this->events_interested_in = $value;

		return true;
	}

	public function company_news($value=NULL) {
		
		if ($value == NULL)
			return $this->company_news;

		if (!is_int($value)) {
			throw new \InvalidArgumentException('Id must be an integer');
			return false;
		}

		$this->company_news = $value;

		return true;
	}

	public function network_activity($value=NULL) {
		
		if ($value == NULL)
			return $this->network_activity;

		if (!is_int($value)) {
			throw new \InvalidArgumentException('Id must be an integer');
			return false;
		}

		$this->network_activity = $value;

		return true;
	}

	public function confirmed($value=NULL) {
		
		if ($value == NULL)
			return $this->confirmed;

		if (!is_int($value)) {
			throw new \InvalidArgumentException('Id must be an integer');
			return false;
		}

		$this->confirmed = $value;

		return true;
	}

	public function act_code($value=NULL) {
		
		if ($value == NULL)
			return $this->act_code;

		if (!is_string($value)) {
			throw new \InvalidArgumentException('Id must be a string');
			return false;
		}

		$this->act_code = $value;

		return true;
	}

	public function img_link($value=NULL) {
		
		if ($value == NULL)
			return $this->img_link;

		if (!is_string($value)) {
			throw new \InvalidArgumentException('Id must be a string');
			return false;
		}

		$this->img_link = $value;

		return true;
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
