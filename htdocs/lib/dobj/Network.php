<?php
namespace dobj;

class Network extends DisplayDObj {

	protected $id_city_cur;
	protected $city_cur;		// varchar(50)
	protected $id_region_cur;
	protected $region_cur;		// varchar(50)
	protected $id_country_cur;
	protected $country_cur;		// varchar(50)
	protected $id_city_origin;
	protected $city_origin;		// varchar(50)
	protected $id_region_origin;
	protected $region_origin;		// varchar(50)
	protected $id_country_origin;
	protected $country_origin;		// varchar(50)
	protected $id_language_origin;
	protected $language_origin;	// varchar(50)
	protected $network_class;

	protected $origin;
	protected $location;

	protected $date_added;		// timestamp
	protected $img_link;
	
	protected $member_count;
	protected $post_count;
	protected $join_date;
	protected $existing;		// bool, not in db

	protected $posts;
	protected $events;
	protected $events_sect;

	public static function createFromId($id, $dal, $do2db) {

		$network = new Network();
		$network->id = $id;

		$network = $do2db->execute($dal, $network, 'getNetworkById');

		// set up origin array
		$origin_keys = array('id_city_origin', 'city_origin', 'id_region_origin',
			'region_origin', 'id_country_origin', 'country_origin',
			'id_language_origin', 'language_origin');
		
		$origin_array = array();

		foreach ($origin_keys as $key) {
			$origin_array[$key] = $network->$key;
		}

		// set up location array
		$location_keys = array('id_city_cur', 'city_cur', 'id_region_cur',
			'region_cur', 'id_country_cur', 'country_cur');

		$location_array = array();

		foreach ($location_keys as $key) {
			$location_array[$key] = $network->$key;
		}

		// now process, separate into origin and 
		$network->origin  = \misc\Util::ArrayToSearchable($origin_array);
		$network->location = \misc\Util::ArrayToSearchable($location_array);

		return $network;
	}

	public function checkRegistration($uid, $dal, $do2db) {

		$args = new \dobj\Blank();
		$args->id_user = $uid;
		$args->id_network = $this->id;

		$result = $do2db->execute($dal, $args, 'checkUserRegistration');

		if (isset($result->user_count) && $result->user_count > 0)
			return true;
		else
			return false;
	}

	public function getPosts($dal, $do2db, $lobound=0, $upbound=10) {

		if ($this->id == NULL) {
			throw new Exception('No id is associated with this network object');
		}

		$args = new Blank();
		$args->id_network = $this->id;
		$args->lobound = $lobound;
		$args->upbound = $upbound;

		$this->posts = $do2db->execute($dal, $args, 'getPostsByNetworkId');

		if (get_class($this->posts) == 'PDOStatement') {
			$this->posts = new DObjList();
		}

		foreach ($this->posts as $post) {
			$post->getImages();
			$post->getReplies($dal, $do2db);
		}
	}

	public function getPostCount($dal, $do2db) {

		if ($this->id == NULL) {
			throw new Exception('No id is associated with this network object');
		}

		$this->post_count = $do2db->execute($dal, $this, 'getPostCount');
	}

	public function getMemberCount($dal, $do2db) {

		if ($this->id == NULL) {
			throw new Exception('No id is associated with this network object');
		}

		$this->member_count = $do2db->execute($dal, $this, 'getMemberCount');
	}

	public function getEvents($dal, $do2db) {

		$result = $do2db->execute($dal, $this, 'getEventsByNetworkId');

		if (get_class($result) == 'PDOStatement') {
			$this->events = new DObjList();
		}
		else  {
			$this->events = $result;
			$sl = $result->splits('month');

			if ($sl != NULL) 
	   		  $this->events_sect = $sl;
		}
	}

	public function display($context) {

	}

	public function getHTML($context, $vars) {

		// get vars
		$cm = $vars['cm'];
		$mustache = $vars['mustache'];

		switch($context) {

		case 'dashboard':

			// get template
			$template = file_get_contents($cm->template_dir . $cm->ds . 'dashboard-network.html');

			return $mustache->render($template, array(
				'active' => true,
				'network' => $this,
				'title' => $this->getTitle(),
				'vars' => $cm->getVars()
				)
			);
			break;
		}
	}

	/*
	 * Parses values and generates title for display
	 * on main site
	 */
	public function getTitle() {

		if ($this->origin == NULL) {

			// set up origin array
			$origin_keys = array('id_city_origin', 'city_origin', 'id_region_origin',
				'region_origin', 'id_country_origin', 'country_origin',
				'id_language_origin', 'language_origin');
			
			$origin_array = array();

			foreach ($origin_keys as $key) {
				$origin_array[$key] = $this->$key;
			}

			$this->origin = \misc\Util::ArrayToSearchable($origin_array);
		}

		if ($this->location == NULL) {

			// set up location array
			$location_keys = array('id_city_cur', 'city_cur', 'id_region_cur',
				'region_cur', 'id_country_cur', 'country_cur');

			$location_array = array();

			foreach ($location_keys as $key) {
				$location_array[$key] = $this->$key;
			}

			$this->location = \misc\Util::ArrayToSearchable($location_array);
		}

		$origin_str = '';
		$location_str = $this->location->toString();

		// figure out type of origin string
		if (get_class($this->origin) == 'dobj\Language') {
			$origin_str = $this->origin->toString().' speakers';
		}
		else {
			$origin_str = 'From '.$this->origin->toString();
		}
		
		return $origin_str . ' in ' . $location_str;
	}
}
