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

	protected $events_attending;
	protected $network_membership;

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

		$result = $do2db->execute($dal, $user, 'getUserById');

		// if its a Pdo statement, couldn't find that user
		if (get_class($result) == 'PDOStatement')
		  return false;
		else {
		  	$result->events_attending = explode(', ', $result->events_attending);
			$result->network_membership = explode(', ', $result->network_membership);
			return $result;
		}
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

	public function activate($dal, $do2db, $act_code) {

		$obj = new \dobj\Blank();
		$obj->id = $this->id;
		$obj->act_code = $act_code;

		$result = $do2db->execute($dal, $obj, 'activateUser');

		if (get_class($result) == 'PDOStatement') 
		  return False;
		else
		  return True;
	}

	public function getEventsInYourNetworks($dal, $do2db) {

		// set query object
		$obj = new Blank();
		$obj->id_user = $this->id;

		// query
		$result = $do2db->execute($dal, $obj, 'getEventsInYourNetworks');

		if (get_class($result) == 'PDOStatement') {
			$err = $result->errorInfo();
			//print_r($err);
			$this->yn_events = NULL;
		}
		else {

			// create section list
			$this->yn_events = $result->splits(array('network', function($obj) {

				// check if event has already occurred
				$event_date = new \DateTime($obj->event_date);
				$now = new \DateTime();

				$result = $event_date > $now;

				if ($result == True) {
					return array(
						'section' => 'active-event',
						'key' => 'active');
				} else {
					return array(
						'section' => 'inactive-event',
						'key' => 'active');
				}
			}), array('inline', 'class')
			);

			// order events
			$this->yn_events->order('active', array(
				'active-event' => 1,
				'inactive-event' => 0
			));
		}
	}

	public function getEventsHosting($dal, $do2db) {

		// set query object
		$obj = new Blank();
		$obj->id_host = $this->id;

		// query
		$result = $do2db->execute($dal, $obj, 'getEventsHosting');

		if (get_class($result) == 'PDOStatement') {
			$err = $result->errorInfo();
			//print_r($err);
			$this->yh_events = NULL;
		}
		else {

			$this->yh_events = $result->splits(array('network', function($obj) {

				// check if event has already occurred
				$event_date = new \DateTime($obj->event_date);
				$now = new \DateTime();

				$result = $event_date > $now;

				if ($result == True) {
					return array(
						'section' => 'active-event',
						'key' => 'active');
				} else {
					return array(
						'section' => 'inactive-event',
						'key' => 'active');
				}
			}), array('inline', 'class')
			);


			// order events
			$this->yh_events->order('active', array(
				'active-event' => 1,
				'inactive-event' => 0
			));
		}
	}

	public function getEventsAttending($dal, $do2db) {

		// set query object
		$obj = new Blank();
		$obj->id_guest = $this->id;

		// query
		$result = $do2db->execute($dal, $obj, 'getEventsAttending');

		if (get_class($result) == 'PDOStatement') {
			$err = $result->errorInfo();
			//print_r($err);
			$this->ya_events = NULL;
		}
		else
			$this->ya_events = $result->splits(array('network', function($obj) {

				// check if event has already occurred
				$event_date = new \DateTime($obj->event_date);
				$now = new \DateTime();

				$result = $event_date > $now;

				if ($result == True) {
					return array(
						'section' => 'active-event',
						'key' => 'active');
				} else {
					return array(
						'section' => 'inactive-event',
						'key' => 'active');
				}
			}), array('inline', 'class')
			);
	}

	public function getPosts($dal, $do2db, $lbound=0, $ubound=11) {

		$obj = new Blank();
		$obj->id_user = $this->id;
		$obj->lbound = $lbound;
		$obj->ubound = $ubound;

		/*
		$this->yp_posts = $do2db->execute($dal, $obj, 'getPostsByUserId');

		if (get_class($this->yp_posts) == 'PDOStatement')
			$this->yp_posts = NULL;
		 */

		$result = $do2db->execute($dal, $obj, 'getPostsByUserId');

		if (get_class($result) == 'PDOStatement') {
			$err = $result->errorInfo();
			//print_r($err);
			$this->yp_posts = NULL;
		}
		else {
		  $this->yp_posts = $result->splits('network');
		}
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

		$result = $do2db->execute($dal, $obj, 'getNetworksByUserId');

		if (get_class($result) == 'PDOStatement') 
		  $this->yn_networks = false;
		else
		  $this->yn_networks = $result; 
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

	public function getJSON() {

		return array(
			'id' => $this->id,
			'email' => $this->email,
			'username' => $this->username,
			'first_name' => $this->first_name,
			'last_name' => $this->last_name,
			'img_link' => $this->img_link,
		);
	}

	/*
	public function prepare($cm, $location='default') {

		if ($location == 'dashboard') {

			// get image thing set up
			if ( !is_file($cm->img_repo_dir . $cm->ds . $this->img_link)) 
			  $this->img_link = '//' . $cm->hostname . $cm->ds . 'images/cm_logo_blank_profile_lrg.png';
			else
			  $this->img_link = $cm->img_host_repo . '/' . $this->img_link;
		}

		return parent::prepare($cm);
	}
	 */

	/*
	 * Necessary for mustache
	 */
	public function __isset($name) {
		return isset($this->$name);
	}

	public function checkNetworkRegistration($nid) {
		return in_array($nid, $this->network_membership);
	}
}

?>
