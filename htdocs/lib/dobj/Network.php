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

	protected $origin_searchable;
	protected $location_searchable;

	protected $date_added;		// timestamp
	protected $img_link;
	
	protected $member_count;
	protected $post_count;
	protected $native_post_count;
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
	protected $query_custom;
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

		// get origin searchable
		if (isset($network->id_city_origin)) {
			$network->origin_searchable = City::createFromId($network->id_city_origin, $dal, $do2db);
		}
		else if (isset($network->id_region_origin)) {
			$network->origin_searchable = Region::createFromId($network->id_region_origin, $dal, $do2db);
		}
		else if (isset($network->id_country_origin)) {
			$network->origin_searchable = Country::createFromId($network->id_country_origin, $dal, $do2db);
		}
		else if (isset($network->id_language_origin)) {
			$network->origin_searchable = Language::createFromId($network->id_language_origin, $dal, $do2db);
		}

		// get location searchable
		if (isset($network->id_city_cur)) {
			$network->location_searchable = City::createFromId($network->id_city_cur, $dal, $do2db);
		}
		else if (isset($network->id_region_cur)) {
			$network->location_searchable = Region::createFromId($network->id_region_cur, $dal, $do2db);
		}
		else if (isset($network->id_country_cur)) {
			$network->location_searchable = Country::createFromId($network->id_country_cur, $dal, $do2db);
		}

		return $network;
	}

	public static function createFromSearchables($origin, $location) {

		$network = new dobj\Network();

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
	 * Load the custom tweet data
	 */
	/*
	public function loadCustomTweetTerms($dal, $do2db) {

		if ($this->id == NULL) {
			throw new Exception('No id is associated with this network object');
		}

		$args = new Blank();
		$args->id_network = $this->id;



		$this->tweets = $do2db->execute($dal, $args, 'getNetworkCustomTweetTerms');

		if (get_class($this->tweets) == 'PDOStatement') {
			$this->tweets = new DObjList();
		}
	}
	 */

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

		$this->native_post_count = (int) $do2db->execute($dal, $this, 'getPostCount');
		$this->post_count += $this->native_post_count + $this->tweet_count;
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
		case 'search':

			$template_array = array(
				'network' => $this,
				'title' => $this->getTitle(),
				'vars' => $cm->getVars()
			);

			if ($this->existing) {
			  $template = file_get_contents($cm->template_dir . $cm->ds . 'user-results_active-network.html');
			}
			else {
			  $template = file_get_contents($cm->template_dir . $cm->ds . 'user-results_possible-network.html');

			  $template_array['origin_class'] = get_class($this->origin_searchable);
			  $template_array['origin_id'] = $this->origin_searchable->id;
			  $template_array['location_class'] = get_class($this->location_searchable);
			  $template_array['location_id'] = $this->location_searchable->id;
			}

			return $mustache->render($template, $template_array); 
		}
	}

	public function getJSON() {

		return array(
			'id' => $this->id,
			'title' => $this->getTitle(),
			'origin_class' => $this->getOriginClass(),
			'origin_id' => $this->getOriginId(),
			'location_class' => $this->getLocationClass(),
			'location_id' => $this->getLocationId(),
			'member_count' => $this->member_count,
			'post_count' => $this->post_count,
			'existing' => $this->existing
		);

	}

	/*
	 * Parses values and generates title for display
	 * on main site
	 */
	public function getTitle() {

		if ($this->origin_searchable !== NULL && $this->location_searchable !== NULL) {

			$origin_str = '';
			$location_str = $this->location_searchable->toString();

			// figure out type of origin string
			if (get_class($this->origin_searchable) == 'dobj\Language') {
				$origin_str = $this->origin_searchable->toString().' speakers';
			}
			else {
				$origin_str = 'From '.$this->origin_searchable->toString();
			}
			
			return $origin_str . ' in ' . $location_str;
		}

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

	public function getOriginId() {

		if ($this->origin_searchable !== NULL) {
			return $this->origin_searchable->id;
		}

		if ($this->origin == NULL) {
			return $this->origin->id;
		}
	}

	public function getOriginClass() {

		if ($this->origin_searchable !== NULL) {
			return get_class( $this->origin_searchable );
		}

		if ($this->origin == NULL) {
			return get_class( $this->origin );
		}
	}

	public function getLocationId() {

		if ($this->location_searchable !== NULL) {
			return $this->location_searchable->id;
		}

		if ($this->location == NULL) {
			return $this->location->id;
		}
	}

	public function getLocationClass() {

		if ($this->location_searchable !== NULL) {
			return get_class( $this->location_searchable );
		}

		if ($this->location == NULL) {
			return get_class( $this->location );
		}
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

			if (isset($this->city_origin)) {

				if ($this->origin_searchable->tweet_terms_override == 0)
					return $this->city_origin;
				else
					return NULL;
			}
					
			if (isset($this->region_origin)) {

				if ($this->origin_searchable->tweet_terms_override == 0)
					return $this->region_origin;
				else
					return NULL;
			}

			if (isset($this->country_origin)) {
				if ($this->origin_searchable->tweet_terms_override == 0)
					return $this->country_origin;
				else
					return NULL;
			}
			
			throw new \Exception('Network: GetOriginComponent no origin set.');

			break;
		case 2:
			if (isset($this->city_origin)) {
				if ($this->origin_searchable->region_tweet_terms_override == 0)
					return $this->region_origin;
				else
					return NULL;
			}
			if (isset($this->region_origin)) {
				if ($this->origin_searchable->country_tweet_terms_override == 0)
					return $this->country_origin;
				else
					return NULL;
			}

			if (isset($this->language_origin))
				throw new \Exception('Network: GetOriginComponent this is a language network, scope == 1');

			throw new \Exception('Network: GetOriginComponent Scope must be below level 2');
			break;
		case 3:
			if (isset($this->city_origin)) {
				if ($this->origin_searchable->country_tweet_terms_override == 0)
					return $this->country_origin;
				else
					return NULL;
			}
			
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
	 * Returns a string or an array of strings
	 */
	public function getOriginComponentArray($component_level) {

		try {
			$default_string = $this->getOriginComponent($component_level);
		}
		catch (\Exception $e) {
			exit('Inside getOriginComponentArray: ' . $e);
		}

		$custom_term_string = $this->getOriginComponentCustomTweetTerm($component_level);

		// return default string if custom_term is NOTHING
		if ($custom_term_string == NULL) {
			return $default_string;
		}
			
		$component = array();
		
		if ($default_string != NULL) {
			$component[] = $default_string;
		}

		// merge the two arrays
		$component = array_merge($component, explode(', ', $custom_term_string));

		return $component;
	}

	public function getOriginComponentId($component_level) {

		switch($component_level) {

		case 1:
			if (isset($this->id_language_origin))
				return $this->id_language_origin;
			if (isset($this->id_city_origin))
				return $this->id_city_origin;
			if (isset($this->id_region_origin))
				return $this->id_region_origin;
			if (isset($this->id_country_origin))
				return $this->id_country_origin;
			
			throw new \Exception('Network: GetOriginComponentId no origin set.');

			break;
		case 2:
			if (isset($this->id_city_origin))
				return $this->id_region_origin;
			if (isset($this->id_region_origin))
				return $this->id_country_origin;

			if (isset($this->id_language_origin))
				throw new \Exception('Network: GetOriginComponentId this is a language network, scope == 1');

			throw new \Exception('Network: GetOriginComponent Scope must be below level 2');
			break;
		case 3:
			if (isset($this->id_city_origin))
				return $this->id_country_origin;
			
			if (isset($this->id_language_origin))
				throw new \Exception('Network: GetOriginComponentId this is a language network, scope == 1');

			throw new \Exception('Network: GetOriginComponentId Scope must be below level 3');
			break;
		default:
			throw new \Exception('Network: GetOriginComponentId cannot find a component with given value: ' . $component_level);
			break;
		}
	}

	/*
	 *
	 * 1 : returns tightest scope
	 * 2 : returns 'middle' scope
	 * 3 : returns broadest scope
	 */
	public function getOriginComponentCustomTweetTerm($component_level) {

		switch($component_level) {

		case 1:
			if (isset($this->city_origin) || isset($this->region_origin) || isset($this->country_origin) || isset($this->language_origin))
				return $this->origin_searchable->tweet_terms;

			throw new \Exception('Network: GetOriginComponentCustomTweetTerm no origin variables are set');

			break;
		case 2:
			if (isset($this->city_origin))
				return $this->origin_searchable->region_tweet_terms;
			if (isset($this->region_origin))
				return $this->origin_searchable->country_tweet_terms;

			throw new \Exception('Network: GetOriginComponentCustomTweetTerm: This origin\'s scope must be level 1');
			break;
		case 3:
			if (isset($this->city_origin))
				return $this->origin_searchable->country_tweet_terms;
			
			throw new \Exception('Network: GetOriginComponent This origin\'s scope must be level 2 or below.');
			break;
		default:
			throw new \Exception('Network: GetOriginComponentCustomTweetTerm: ' . $component_level . ' is not a valid scope. Outside range.');
			break;
		}
	}

	/*
	 * Gives the current location as specified by query scope
	 *
	 */
	public function getQueryOriginComponent($class='string') {
		
		if ($class == 'array') {
			return $this->getOriginComponentArray($this->query_origin_scope);
		}
		
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
	public function getLocationComponentCustomTweetTerm($component_level) {

		switch($component_level) {

		case 1:
			if (isset($this->city_cur) || isset($this->region_cur) || isset($this->country_cur))
				return $this->location_searchable->tweet_terms;

			throw new \Exception('Network: GetLocationComponent no location variables are set');

			break;
		case 2:
			if (isset($this->city_cur))
				return $this->location_searchable->region_tweet_terms;
			if (isset($this->region_cur))
				return $this->location_searchable->country_tweet_terms;

			throw new \Exception('Network: GetLocationComponent: This location\'s scope must be level 1');
			break;
		case 3:
			if (isset($this->city_cur))
				return $this->location_searchable->country_tweet_terms;
			
			throw new \Exception('Network: GetLocationComponent This location\'s scope must be level 2 or below.');
			break;
		default:
			throw new \Exception('Network: GetLocationComponent: ' . $component_level . ' is not a valid scope. Outside range.');
			break;
		}
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

			if (isset($this->city_cur)) {

				if ($this->location_searchable->tweet_terms_override == 0)
					return $this->city_cur;
				else
					return NULL;
			}
					
			if (isset($this->region_cur)) {

				if ($this->location_searchable->tweet_terms_override == 0)
					return $this->region_cur;
				else
					return NULL;
			}

			if (isset($this->country_cur)) {
				if ($this->location_searchable->tweet_terms_override == 0)
					return $this->country_cur;
				else
					return NULL;
			}
			
			throw new \Exception('Network: GetLocationComponent no location set.');

			break;
		case 2:
			if (isset($this->city_cur)) {
				if ($this->location_searchable->region_tweet_terms_override == 0)
					return $this->region_cur;
				else
					return NULL;
			}

			if (isset($this->region_cur)) {
				if ($this->location_searchable->country_tweet_terms_override == 0)
					return $this->country_cur;
				else
					return NULL;
			}

			throw new \Exception('Network: GetLocationComponent Scope must be below level 2');
			break;
		case 3:
			if (isset($this->city_cur)) {
				if ($this->location_searchable->country_tweet_terms_override == 0)
					return $this->country_cur;
				else
					return NULL;
			}
			
			throw new \Exception('Network: GetLocationComponent Scope must be below level 3');
			break;
		default:
			throw new \Exception('Network: GetLocationComponent cannot find a component with given value: ' . $component_level);
			break;
		}
		/*
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
		 */
	}

	/*
	 *
	 * 1 : returns tightest scope
	 * 2 : returns 'middle' scope
	 * 3 : returns broadest scope
	 */
	public function getLocationComponentId($component_level) {

		switch($component_level) {

		case 1:
			if (isset($this->id_city_cur))
				return $this->id_city_cur;
			if (isset($this->id_region_cur))
				return $this->id_region_cur;
			if (isset($this->id_country_cur))
				return $this->id_country_cur;
			
			throw new \Exception('Network: GetLocationComponent no location variables are set');

			break;
		case 2:
			if (isset($this->id_city_cur))
				return $this->id_region_cur;
			if (isset($this->id_region_cur))
				return $this->id_country_cur;

			throw new \Exception('Network: GetLocationComponent: This location\'s scope must be level 1');
			break;
		case 3:
			if (isset($this->id_city_cur))
				return $this->id_country_cur;
			
			throw new \Exception('Network: GetLocationComponent This location\'s scope must be level 2 or below.');
			break;
		default:
			throw new \Exception('Network: GetLocationComponent: ' . $component_level . ' is not a valid scope. Outside range.');
			break;
		}
	}

	/*
	 * Returns a string or an array of strings
	 */
	public function getLocationComponentArray($component_level) {

		try {
			$default_string = $this->getLocationComponent($component_level);
		}
		catch (\Exception $e) {
			exit('Inside getLocationComponentArray: ' . $e);
		}

		$custom_term_string = $this->getLocationComponentCustomTweetTerm($component_level);

		// return default string if custom_term is NOTHING
		if ($custom_term_string == NULL) {
			return $default_string;
		}
			
		$component = array();

		if ($default_string != NULL) {
			$component[] = $default_string;
		}

		// merge the two arrays
		$component = array_merge($component, explode(', ', $custom_term_string));

		return $component;
	}

	/*
	 * Gives the current location as specified by query scope
	 *
	 */
	public function getQueryLocationComponent($class='string') {
		
		if ($class == 'array') {
			return $this->getLocationComponentArray($this->query_location_scope);
		}
		
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

	public function getNetworkQueryRoster() {

		/*
		 * Order!
		 *
		 * - Both
		 * - Origin
		 * - Location
		 */
		$initial_roster = array();
		$key_array = array();

		$cur_roster_origin_level = $this->query_origin_scope;
		$cur_roster_location_level = $this->query_location_scope;
		$cur_roster_origin_level_max = $this->getMaxOriginScope();
		$cur_roster_location_level_max = $this->getMaxLocationScope();
		$finished = False;

		$last_updated = NULL;
		$skip_origin = False;
		$skip_location = False;
		$key_count = 0;	// count for keeping track of keys

		// COMPLICATED LOOP
		//
		// Creating first roster of things
		//
		// Iterates until the break statement is reached
		//
		while (True)
		{
			// get both
			$origin = $this->getOriginComponentId($cur_roster_origin_level);
			$location = $this->getLocationComponentId($cur_roster_location_level);


			// BOTH
			if ($last_updated == NULL || $last_updated == 'location') {


				// Figure out what goes in here later
				array_push($initial_roster, array(
					'component' => 'both',
					'value' => array($origin, $location),
					'origin_level' => $cur_roster_origin_level,
					'location_level' => $cur_roster_location_level
				));

				// increase key_count
				$key_count++;

				$last_updated = 'both';
			}

			// ORIGIN
			if ($last_updated == 'both') {

				if ($skip_origin === False) {

					array_push($initial_roster, array(
						'component' => 'origin',
						'value' => $origin,
						'level' => $cur_roster_origin_level
					));

					$key_array[$origin] = $key_count;

					// increase key_count
					$key_count++;
				}

				// increment level
				if ($cur_roster_origin_level < $cur_roster_origin_level_max) {
					$cur_roster_origin_level++;
				}
				else {
					$skip_origin = True;
				}

				$last_updated = 'origin';
			}
			
			// LOCATION
			if ($last_updated == 'origin') {
				
				if ($skip_location === False) {

					array_push($initial_roster, array(
						'component' => 'location',
						'value' => $location,
						'level' => $cur_roster_location_level
					));
					
					$key_array[$location] = $key_count;

					// increase key_count
					$key_count++;
				}

				// UPDATE
				if ($cur_roster_location_level < $cur_roster_location_level_max) {
					$cur_roster_location_level++;
				}
				else {
					$skip_location = True;
				}

				$last_updated = 'location';
			}

			// TRACKING
			if ($skip_location == True && $skip_origin == True) {

				// end loop
				break;
			}
		}

		// now I have two arrays
		// initial_roster, and key_array
		//

		$suspect_keys = array();
		$last_value = 0;

		 // Key array will most def be arranged in ascending order
		foreach($key_array as $location => $key) {

			$difference = $key - $last_value;

			// if difference is greater than one, it means we've skipped something
			if ($difference > 1) {

				$suspect_keys = array_merge($suspect_keys, range(($last_value+1), ($key-1)));
			}
			
			// make sure first (zero) value is chosen
			//  -- Shouldn't need it, but just in case
			if ($difference == 1 && $last_value == 0) {

				array_push($suspect_keys, $last_value);
			}

			// set us up for the next one
			$last_value = $key;
		}

		// check that some final value was not skipped (necessary?)
		//


		// loop through suspect keys and find bad keys and DESTROY THEM
		foreach($suspect_keys as $key) {

			// UNSET IF IT HAS BEEN MARKED
			if ($initial_roster[$key]['component'] !== 'both') {

				unset($initial_roster[$key]);
			}

			// UNSET IF BOTH component has DUPLICATE VALUES
			//
			if ($initial_roster[$key]['component'] == 'both') {

				$value_1 = $initial_roster[$key]['value'][0];
				$value_2 = $initial_roster[$key]['value'][1];

				if ($value_1 === $value_2) {
					unset($initial_roster[$key]);
				}
			}
		}

		// return newly indexed array
		return array_values($initial_roster);
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

	public function writeCustomQuery($dal, $do2db) {

		$result = $do2db->execute($dal, $this, 'writeNetworkCustomQuery');
	}

	public function deleteCustomQuery($dal, $do2db) {

		$result = $do2db->execute($dal, $this, 'deleteNetworkCustomQuery');
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
