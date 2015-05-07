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

		$something = ''
	) 
	{

		// new cache object
		$cache = new \misc\Cache($this->cm);

		$tweet_key = 'n' . $this->network->id . '_tweets';
		$tweet_info_key = $tweet_key . '_info';
		$tweets_exist = $cache->exists($tweet_key);

		if ($tweets_exist == False || $mode == 'adjust') {

			// make an api call to the lords of twitter
			$twitter_query = new TwitterQuery();
			$twitter_query->buildSearch($this->network);
			$search_scope = $this->network->getScopeInfo();
			$search_terms = $twitter_query->getTerms();

			// do the thing
			$twitter_call = new TwitterApiCall($this->cm, $twitter_query);
			$twitter_json = $twitter_call->execute();

			// create Remora
			$remora = new \dal\Remora();
			$remora->count = 0;
			$remora->search_scope = $search_scope;
			$remora->search_terms = $search_terms;
			$remora->relevance_array = array();
			$remora->constants = $equation_constants;

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
			});

			// get relevance index from remora data
			$tweets = Twitter::JsonToTweets($twitter_json, $remora);

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

			// add tweets to cache
			$TIME_TO_LIVE = 30; // 30 minutes, or two call cycles
			$cache->add($tweet_key, $tweets, $TIME_TO_LIVE * 60);

			// fill query info
			$this->query_info = array(
				'relevance' => $query_efficacy,
				'max_count' => $this->MAX_RESULT_COUNT,
				'result_count' => $relevance_count,
				'since_date' => $this->network->query_since_date,
				'query' => urldecode( $twitter_query->getQuery() ),
				'origin_weight' => $equation_constants['origin_weight'],
				'location_weight' => $equation_constants['location_weight'],
				'count_weight' => $equation_constants['count_weight']
			);

			$cache->add($tweet_info_key, $this->query_info);
		}
		else
		{
			$tweets = $cache->fetch($tweet_key);
			$this->query_info = $cache->fetch($tweet_info_key);
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
}