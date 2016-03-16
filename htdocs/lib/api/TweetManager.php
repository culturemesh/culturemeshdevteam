<?php
namespace api;

class TweetManager {

	/*
	 * CultureMesh
	 */
	private $cm;

	/*
	 * The current network
	 */
	private $network;

	// Maximum results from a query
	private $MAX_RESULT_COUNT = 15;

	private $RELEVANCE_METRIC = 1;

	private $query_info;

	private $dal;
	private $do2db;

	private $blocked_users;

	/*
	 * Constructor
	 *
	 * Fills variables as needed.
	 *
	 * @param - $cm : The environment
	 * @param - $network : The network making the call
	 *
	 * @exception - Thrown if either cm or network are not correct type
	 *
	 */
	public function __construct($cm, $network, $dal, $do2db) {

		if (get_class($cm) != 'Environment')
			throw new \Exception('TweetManager: No environment variable passed.');

		if (get_class($network) != 'dobj\Network')
			throw new \Exception('TweetManager: No network variable passed.');

		$this->cm = $cm;
		$this->network = $network;
		$this->dal = $dal;
		$this->do2db = $do2db;

		$this->blocked_users = array('asianchicks_xxx');
	}

	/*
	 * Requests Tweets
	 *
	 * Fills variables as needed.
	 *
	 * @param - $cm : The environment
	 * @param - $network : The network making the call
	 *
	 * @exception - Thrown if either cm or network are not correct type
	 *
	 */
	public function requestTweets($mode = 'network',
		
		$equation_constants = array(
			'location_weight' => 1,
			'origin_weight' => 1,
			'count_weight' => 1
		),

		$params = array(
			'until_date' => '2010-01-01',
			'component' => 'location'
		)
	) 
	{

		// new cache object
		$cache = new \misc\Cache($this->cm);

		if ( $mode == 'network' || $mode == 'adjust') {
			$tweet_key = 'n' . $this->network->id . '_tweets';
		}

		if ( $mode == 'network_addtl') {

			$qls = NULL;
			$key_until_date = 'initial';

			if ($params['component'] == 'location') {
				$qls = $this->network->query_location_scope;
			}
			if ($params['component'] == 'origin') {
				$qls = $this->network->query_origin_scope;
			}
			if ($params['component'] == 'both') {
				$qls = $this->network->query_location_scope . '+' . $this->network->query_origin_scope;
			}

			if ($params['until_date'] != "") {
				$key_until_date = $params['until_date'];
			}

			$tweet_key = 'n' . $this->network->id . '_addtl_tweets_' . $params['component'] . '_' . $qls . '_' . $key_until_date;
		}

		$tweet_info_key = $tweet_key . '_info';
		$tweets_exist = $cache->exists($tweet_key) && $this->cm->cachingTweets();

		// proceed straight to query if mode is 'network_addtl' or 'adjust'
		if ($tweets_exist === False || $mode == 'adjust') {

			/*
			// make an api call to the lords of twitter
			$twitter_query = new TwitterQuery();

			if ($mode == 'network_addtl') {

				if (!isset($params['until_date']))
					throw new \Exception('No until date set in Tweet Manager');

				$twitter_query->includeUntilDate($params['until_date']);
			}

			$twitter_query->buildSearch($this->network);
			$search_scope = $this->network->getScopeInfo();
			$search_terms = $twitter_query->getTerms();

			// do the thing
			$twitter_call = new TwitterApiCall($this->cm, $twitter_query);
			$twitter_json = $twitter_call->execute();
			 */

			$twitter_query = NULL;

			if ($mode == 'network_addtl') {

				if (!isset($params['until_date']))
					throw new \Exception('No until date set in Tweet Manager');

				if ($params['component'] == 'both') {
					$twitter_query = new TwitterQuery();
					$twitter_query->buildSearch($this->network);
					$twitter_query->includeUntilDate($params['until_date']);
				}
				else {
					$twitter_query = new ComponentTwitterQuery();
					$twitter_query->buildSearch($this->network, $params['component']);
					$twitter_query->includeUntilDate($params['until_date']);
				}
			}
			else {

				$twitter_query = new TwitterQuery();
				$twitter_query->buildSearch($this->network);
			}

			$search_scope = $this->network->getScopeInfo();
			$search_terms = $twitter_query->getTerms();

			// do the thing
			$twitter_call = new TwitterApiCall($this->cm, $twitter_query);

			// returns JSON of twitter stuffs
			$twitter_json = $twitter_call->execute();

			// create Remora
			$remora = new \dal\Remora();

			if ($mode == 'network' || $mode=='adjust') {

				$remora->count = 0;
				$remora->search_scope = $search_scope;
				$remora->search_terms = $search_terms;
				$remora->relevance_array = array();
				$remora->constants = $equation_constants;
				$remora->string_array = array();
				$remora->blocked_users = $this->blocked_users;
				$remora->earliest_tweet_date = NULL;

				$remora->test_array = array("vegetable");

				// remora function
				$remora->setFunction(function($obj) {

					// check the relevance of a particular tweet
					$this->count += 1;

					// get term count
					$term_count_expected = count($this->search_terms);
					$term_count_actual = 0;

					foreach ($this->search_terms as $term) {

						if (strpos($obj->text, $term) > -1)
								$term_count_actual++;
					}

					$term_count_ratio_raw = $term_count_actual / $term_count_expected;

					// something about time here

					$origin_scope_ratio = $this->constants['origin_weight'] * $this->search_scope['origin_scope_ratio'];
					$location_scope_ratio = $this->constants['location_weight'] * $this->search_scope['location_scope_ratio'];
					$term_count_ratio = $this->constants['count_weight'] * $term_count_ratio_raw;

					// get relevance
					$tweet_relevance = $term_count_ratio * $this->search_scope['origin_scope_ratio'] * $this->search_scope['location_scope_ratio'];

					// must do it in this complicated way, because
					// the registry makes array access complicated
					//
					$arr = $this->relevance_array;
					$arr[] = $tweet_relevance;

					$this->relevance_array = $arr;

					// check if this tweet has been duplicated
					//
					// if not...

					$tagged_string = false;
					$in_array = false;
					$match = '#https\:\/\/t\.co\/[a-zA-Z0-9-]+#';
					$text_no_links = preg_replace($match, "", $obj->text);

					// Check to see if 
					//   string without tags equals original string
					//     and then check to see if the value is in the array

					// again, must use because of complications
					$arr = $this->string_array;

					// a little more perfect, must find out how to
					//   use arrays as needle in in_array function
					if ($text_no_links !== $obj->text) {
						$tagged_string = True;
						$in_array = in_array($text_no_links, $arr) || in_array($obj->text);
					}
					else {
					  $in_array = in_array($obj->text, $arr);
					}

					// If in array, mark as duplicate
					//   if not, add new values to array
					if ($in_array) {
						$obj->duplicate = True;
					}
					else {
						$obj->duplicate = False;

						// push things into array
						$arr[] = $obj->text;

						// also push copy without links
						if ($tagged_string)
						  $arr[] = $text_no_links;

						$this->string_array = $arr;
					}

					if (in_array($obj->user['screen_name'], $this->blocked_users)) {
						$obj->blocked = True;
					}
					else {
						$obj->blocked = False;
					}

					$this->earliest_tweet_date = $obj->created_at;

				});
			}

			if ($mode == 'network_addtl') {

				$remora->earliest_tweet_date = NULL;

				// capture earliest tweet date
				$remora->setFunction(function($obj) {
					$this->earliest_tweet_date = $obj->created_at;
				});
			}

			// get relevance index from remora data
			$tweets = Twitter::JsonToTweets($twitter_json, $remora);

			// get earliest tweet date
			if (count($tweets) > 0) {
				$remora->earliest_tweet_date = $tweets->end()->created_at;
			}
			else {
				$remora->earliest_tweet_date = NULL;
			}

			if ($mode == 'network' || $mode=='adjust') {

				$relevance_count = count( $remora->relevance_array );
				$count_ratio = $relevance_count / $this->MAX_RESULT_COUNT;
				$mean_relevance = array_sum( $remora->relevance_array ) / $relevance_count;
				$query_efficacy = $mean_relevance * $count_ratio;

				if ($relevance_index >= $this->RELEVANCE_METRIC) {

					// recommend changes to the query
					//echo 'Increase scope';
				}
				else {
					//echo 'Increase level';
				}

				// update tweet count
				$this->network->updateTweetCount($this->dal, $this->do2db, $remora->count);
			}

			// add tweets to cache
			$TIME_TO_LIVE = 30; // 30 minutes, or two call cycles

			if ($mode == 'network_addtl') {
				$TIME_TO_LIVE = 1440; // 24 hours
			}

			$cache->add($tweet_key, $tweets, $TIME_TO_LIVE * 60);

			// fill query info
			$this->query_info = array(
				'relevance' => $query_efficacy,
				'max_count' => $this->MAX_RESULT_COUNT,
				'result_count' => $relevance_count,
				'since_date' => $this->network->query_since_date,
				'until_date' => $remora->earliest_tweet_date,
				'query' => urldecode( $twitter_query->getQuery() ),
				'component_string' => urldecode( $twitter_query->getComponentString() ),
				'origin_weight' => $equation_constants['origin_weight'],
				'location_weight' => $equation_constants['location_weight'],
				'count_weight' => $equation_constants['count_weight'],
				'tweet_key' => $tweet_key,
				'tweet_info_key' => $tweet_info_key,
				'previously_cached' => False
			);

			$cache->add($tweet_info_key, $this->query_info, $TIME_TO_LIVE * 60);
		}
		else if ($tweets_exist) //&& $mode != 'network_addtl')
		{
			$this->query_info['previously_cached'] = True;

			if ($mode == 'network_addtl') {
				$tweets = $cache->fetch($tweet_key);
				$this->query_info = $cache->fetch($tweet_info_key);
			}

			if ($mode == 'network' || $mode == 'adjust') {
				$tweets = $cache->fetch($tweet_key);
				$this->query_info = $cache->fetch($tweet_info_key);
			}
		}

		return $tweets;
	}

	/*
	 * Returns information about the previous query
	 *
	 * @returns - array of information
	 * 	relevance
	 * 	result_count
	 * 	query
	 */
	public function getQueryInfo() {
		return $this->query_info;
	}

	public function checkCache() {

	}
}
