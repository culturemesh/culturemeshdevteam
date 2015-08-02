<?php
namespace api;

class TwitterApiCall {

	private $cm;
	private $query;

	public function __construct($cm, $query) {

		if (get_class($cm) != 'Environment')
			throw new \Exception('TwitterApiCall: Not a valid Environment object');

		if (!in_array(get_class($query), array('api\TwitterQuery', 'api\ComponentTwitterQuery')))
			throw new \Exception('TwitterApiCall: Not a valid TwitterQuery object');

		$this->cm = $cm;
		$this->query = $query;

	}

	public function execute() {

		// twitter api call
		$settings = array(
		    'oauth_access_token' => $GLOBALS['TWITTER_OAUTH_ACCESS_TOKEN'],
		    'oauth_access_token_secret' => $GLOBALS['TWITTER_OAUTH_ACCESS_SECRET'],
		    'consumer_key' => $GLOBALS['TWITTER_CONSUMER_KEY'],
		    'consumer_secret' => $GLOBALS['TWITTER_CONSUMER_SECRET']
		);

		$url = $this->query->getBaseUrl();
		$getfield = $this->query->getQuery();
		$requestMethod = 'GET';

		$twitter = new TwitterAPIExchange($settings);
		$dump = $twitter->setGetfield($getfield)
			     ->buildOauth($url, $requestMethod)
			     ->performRequest();

		return json_decode($dump, true);
	}
}
