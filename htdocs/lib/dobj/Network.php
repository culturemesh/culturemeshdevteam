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

	protected $query_origin_scope;
	protected $query_location_scope;
	protected $query_level;
	protected $query_auto_update;
	protected $query_default;
	protected $query_still_date;
	protected $tweet_count;

	public static function createFromId($id, $dal, $do2db) {

		$network = new Network();
		$network->id = $id;

		$network = $do2db->execute($dal, $network, 'getNetworkById');

		if (get_class($network) == 'PDOStatement')
			return false;

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

	/*
	 * Returns info necessary for generating
	 * twitter search queries
	 */
	public function getTwitterInfo() {

		return array(
			'error' => 0
		);
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

		// single post count
		$value = $do2db->execute($dal, NULL, 'selectFoundRows');

		if (get_class($this->posts) == 'PDOStatement') {
			$this->posts = new DObjList();
		}
		
		$this->more_newer_posts = false;
	
		if ($value > 10) {
			$this->more_older_posts = true;
		}

		else {
			$this->more_older_posts = false;
		}

		foreach ($this->posts as $post) {
			$post->getImages();
			$post->getReplies($dal, $do2db);

		}
	}

	public function getOlderPostsFromId($dal, $do2db, $pid, $lobound=0, $upbound=10) {

		if ($this->id == NULL) {
			throw new Exception('No id is associated with this network object');
		}

		$args = new Blank();
		$args->id = $pid;
		$args->id_network = $this->id;
		$args->lobound = $lobound;
		$args->upbound = $upbound;

		$this->posts = $do2db->execute($dal, $args, 'getOlderPostsFromId');

		// single post count
		$value = $do2db->execute($dal, NULL, 'selectFoundRows');

		if (get_class($this->posts) == 'PDOStatement') {
			$this->posts = new DObjList();
		}

		/*
		$this->more_posts = array(
			'more' => false,
			'newer_posts' => true,
			'newer_posts_pid' => $pid,
			'older_posts' => false,
			'older_posts_pid' => $pid
			);

		$this->more_newer_posts = true;
	
		if ($value > 10) {
			$this->more_posts['older_posts'] = true;
		}*/
		
		$this->more_newer_posts = false;
	
		if ($value > 10) {
			$this->more_older_posts = true;
		}

		else {
			$this->more_older_posts = false;
		}

		foreach ($this->posts as $post) {
			$post->getImages();
			$post->getReplies($dal, $do2db);
		}
	}

	public function getNewerPostsFromId($dal, $do2db, $pid, $lobound=0, $upbound=10) {

		if ($this->id == NULL) {
			throw new Exception('No id is associated with this network object');
		}

		$args = new Blank();
		$args->id = $this->id;
		$args->id_network = $this->id;
		$args->lobound = $lobound;
		$args->upbound = $upbound;

		$this->posts = $do2db->execute($dal, $args, 'getNewerPostsFromId');

		if (get_class($this->posts) == 'PDOStatement') {
			$this->posts = new DObjList();
		}
		else {
			$this->more_newer_posts = false;
			if ($value > 10) {
				$this->more_newer_posts = true;
			}

			$this->posts->reverse();
		}

		foreach ($this->posts as $post) {
			$post->getImages();
			$post->getReplies($dal, $do2db);
		}
	}

	public function getTweets($dal, $do2db, $lobound=0, $upbound=10) {

		if ($this->id == NULL) {
			throw new Exception('No id is associated with this network object');
		}

		$args = new Blank();
		$args->id_network = $this->id;
		$args->lobound = $lobound;
		$args->upbound = $upbound;

		$this->tweets = $do2db->execute($dal, $args, 'getTweetsByNetworkId');

		if (get_class($this->tweets) == 'PDOStatement') {
			$this->tweets = new DObjList();
		}

		foreach ($this->tweets as $tweet) {
			$tweet->getReplies($dal, $do2db);
		}
	}

	/* 
	 * Merges all posts with all tweets
	 * Makes sure no tweets are duplicated (as can happen with cached stuff)
	 *
	 * Not the cleanest solution, but I'm under a time crunch at the moment
	 * Will take out the parameter later and get it below O(n^2)
	 *
	 */
	public function mergePostsAndTweets($api_tweets) {

		// get saved tweets, check to see that they aren't in api tweets
		foreach ($this->tweets as $tweet) {

			for($i=0; $i<count($api_tweets); $i++) {

				if( $api_tweets[$i]->id == (int) $tweet->id_twitter ) {

					unset($api_tweets[$i]);
					$api_tweets->array_values();
				}
			}
		}

		// merge all the arrays
		$this->posts->merge($this->tweets);
		$this->posts->merge($api_tweets);
	}

	public function getPostCount($dal, $do2db) {

		if ($this->id == NULL) {
			throw new Exception('No id is associated with this network object');
		}

		$this->post_count = $do2db->execute($dal, $this, 'getPostCount');
		$this->post_count += $this->tweet_count;
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

	/*
	 * Returns array of info required to make Twitter API queries
	 */
	public function getTwitterApiInfo() {

		// get location data
		$location_name = $this->getLowestLocationComponent();

		if (isset($this->language_origin)) { 

			$origin_language_code = someFunction($this->language_origin);

			return array(
				'location_name' => $location_name,
				'language_origin' => $this->language_origin,
				'language_code' => $origin_language_code
			);
		}
		else {

		}
	}

	/*
	 * Returns lowest component of origin for this particular
	 * network 
	 *
	 * e.g. city < region < country
	 */
	public function getLowestOriginComponent() {

		if (isset($this->language_origin))
			return $this->language_origin;
		if (isset($this->city_origin))
			return $this->city_origin;
		if (isset($this->region_origin))
			return $this->region_origin;
		if (isset($this->country_origin))
			return $this->country_origin;

		throw new \Exception("There is no origin data set for this network");
	}

	/*
	 * Returns highest component of origin for this particular
	 * network 
	 *
	 * e.g. country > region > city  
	 */
	public function getHighestOriginComponent() {

		if (isset($this->language_origin))
			return $this->language_origin;
		if (isset($this->country_origin))
			return $this->country_origin;
		if (isset($this->region_origin))
			return $this->region_origin;
		if (isset($this->city_origin))
			return $this->city_origin;

		throw new \Exception("There is no origin data set for this network");
	}


	/*
	 * Returns lowest component of location for this particular
	 * network 
	 *
	 * e.g. city < region < country
	 */
	public function getLowestLocationComponent() {

		if (isset($this->city_cur))
			return $this->city_cur;
		if (isset($this->region_cur))
			return $this->region_cur;
		if (isset($this->country_cur))
			return $this->country_cur;

		throw new \Exception("There is no location data set for this network");
	}

	/*
	 * A series of functions that returns scopes and components
	 *
	 * Get Component
	 * @params - $component_level - a number from 1 to 3
	 * 	1: language/country
	 * 	2: region
	 * 	3: city
	 *
	 */
	public function getOriginScope() {

		if (isset($this->language_origin))
			return 1;
		if (isset($this->city_origin))
			return 3;
		if (isset($this->region_origin))
			return 2;
		if (isset($this->country_origin))
			return 1;
	}

	public function getMinOriginScope() {
		return 1;
	}


	public function getMaxOriginScope() {

		if (isset($this->language_origin))
			return 1;
		if (isset($this->city_origin))
			return 3;
		if (isset($this->region_origin))
			return 2;
		if (isset($this->country_origin))
			return 1;
	}

	public function getOriginComponent($component_level) {

		switch($component_level) {

		case 1:
			if (isset($this->language_origin))
				return $this->language_origin;
			if (isset($this->city_origin))
				return $this->city_origin;
			if (isset($this->region_origin))
				return $this->region_origin;
			if (isset($this->country_origin))
				return $this->country_origin;
			
			throw new \Exception('Network: GetOriginComponent no origin set.');

			break;
		case 2:
			if (isset($this->city_origin))
				return $this->region_origin;
			if (isset($this->region_origin))
				return $this->country_origin;

			if (isset($this->language_origin))
				throw new \Exception('Network: GetOriginComponent this is a language network, scope == 1');

			throw new \Exception('Network: GetOriginComponent Scope must be below level 2');
			break;
		case 3:
			if (isset($this->city_origin))
				return $this->country_origin;
			
			if (isset($this->language_origin))
				throw new \Exception('Network: GetOriginComponent this is a language network, scope == 1');

			throw new \Exception('Network: GetOriginComponent Scope must be below level 3');
			break;
		default:
			throw new \Exception('Network: GetOriginComponent cannot find a component with given value: ' . $component_level);
			break;
		}
	}

	/*
	 * Gives the current location as specified by query scope
	 *
	 */
	public function getQueryOriginComponent() {
		
		return $this->getOriginComponent($this->query_origin_scope);
	}

	public function getLocationScope() {

		if (isset($this->city_cur))
			return 3;
		if (isset($this->region_cur))
			return 2;
		if (isset($this->country_cur))
			return 1;
	}

	public function getMinLocationScope() {
		return 1;
	}

	public function getMaxLocationScope() {

		if (isset($this->city_cur))
			return 3;
		if (isset($this->region_cur))
			return 2;
		if (isset($this->country_cur))
			return 1;
	}

	/*
	 *
	 * 1 : returns tightest scope
	 * 2 : returns 'middle' scope
	 * 3 : returns broadest scope
	 */
	public function getLocationComponent($component_level) {

		switch($component_level) {

		case 1:
			if (isset($this->city_cur))
				return $this->city_cur;
			if (isset($this->region_cur))
				return $this->region_cur;
			if (isset($this->country_cur))
				return $this->country_cur;
			
			throw new \Exception('Network: GetLocationComponent no location variables are set');

			break;
		case 2:
			if (isset($this->city_cur))
				return $this->region_cur;
			if (isset($this->region_cur))
				return $this->country_cur;

			throw new \Exception('Network: GetLocationComponent: This location\'s scope must be level 1');
			break;
		case 3:
			if (isset($this->city_cur))
				return $this->country_cur;
			
			throw new \Exception('Network: GetLocationComponent This location\'s scope must be level 2 or below.');
			break;
		default:
			throw new \Exception('Network: GetLocationComponent: ' . $component_level . ' is not a valid scope. Outside range.');
			break;
		}
	}


	/*
	 * Gives the current location as specified by query scope
	 *
	 */
	public function getQueryLocationComponent() {
		
		return $this->getLocationComponent($this->query_location_scope);
	}

	/*
	 * Returns information relevant to the tweet manager
	 * Gives network scope, query scope, and (1 / the ratio between the two)
	 *
	 */
	public function getScopeInfo() {

		return array(
			'query_origin_scope' => $this->query_origin_scope,
			'max_origin_scope' => $this->getMaxOriginScope(),
			'origin_scope_ratio' => $this->getOriginScopeRatio(),
			'query_location_scope' => $this->query_location_scope,
			'max_location_scope' => $this->getMaxLocationScope(),
			'location_scope_ratio' => $this->getLocationScopeRatio()
		);
	}

	/*
	 * Is (1) unless scope has been broadened
	 *
	 */
	public function getOriginScopeRatio() {
		return $this->query_origin_scope / 1;
	}

	/*
	 * Returns (1) unless scope has been broadened
	 *
	 */
	public function getLocationScopeRatio() {
		return $this->query_location_scope / 1;
	}

	public function getDistanceToMaxLevel() {

		$max_level = $this->getMaxOriginScope() * $this->getMaxLocationScope() * 3;
		$cur_level = ($this->query_origin_scope * $this->query_location_scope) + $this->query_level;
		$distance = $max_level - $cur_level;

		return $distance;
	}

	public function getDistanceToMinLevel() {

		$min_level = 0;
		$cur_level = ($this->query_origin_scope * $this->query_location_scope) + $this->query_level;
		$distance = $cur_level - 0;

		return $distance;
	}

	public function getMaxLevel() {
		return 2;
	}

	public function getMinLevel() {
		return 0;
	}

	public function adjustTwitterQuery($dal, $do2db) {

		$result = $do2db->execute($dal, $this, 'updateNetworkTweetQuery');
	}

	public function updateTweetCount($dal, $do2db, $query_tweet_count) {

		$this->tweet_count += $query_tweet_count;

		$result = $do2db->execute($dal, $this, 'updateNetworkTweetCount');
	}

	public function decrementTweetCount($dal, $do2db) {

		$this->tweet_count -= 1;
		$result = $do2db->execute($dal, $this, 'updateNetworkTweetCount');
	}
}
