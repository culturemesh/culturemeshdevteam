<?php
namespace dobj;

class Tweet extends Post {

	protected $annotations;
	protected $contributors;
	protected $coordinates;
	protected $created_at;
	protected $current_user_retweet;
	protected $entities;
	protected $favorite_count;
	protected $favorited;
	protected $filter_level;
	protected $geo;
	protected $id;
	protected $id_str;
	protected $in_reply_to_screen_name;
	protected $in_reply_to_status_id;
	protected $in_reply_to_status_id_str;
	protected $in_reply_to_user_id;
	protected $in_reply_to_user_id_str;
	protected $lang;
	protected $place;
	protected $possibly_sensitive;
	protected $scopes;
	protected $retweet_count;
	protected $retweeted;
	protected $retweeted_status;
	protected $source;
	protected $text;
	protected $truncated;
	protected $user;
	protected $withheld_copyright;
	protected $withheld_in_countries;
	protected $withheld_scope;

	/*
	 * Fills up Tweet object with properties (usually)
	 *  from a json encoded twitter api call
	 *
	 */
	public function fillFromJson($json_tweet) {

		$this->annotations = $json_tweet['annotations'];
		$this->contributors = $json_tweet['contributors'];
		$this->coordinates = $json_tweet['coordinates'];
		$this->created_at = $json_tweet['created_at'];
		$this->current_user_retweet = $json_tweet['current_user_retweet'];
		$this->entities = $json_tweet['entities'];
		$this->favorite_count = $json_tweet['favorite_count'];
		$this->favorited = $json_tweet['favorited'];
		$this->filter_level = $json_tweet['filter_level'];
		$this->geo = $json_tweet['geo'];
		$this->id = $json_tweet['id'];
		$this->id_str = $json_tweet['id_str'];
		$this->in_reply_to_screen_name = $json_tweet['in_reply_to_screen_name'];
		$this->in_reply_to_status_id = $json_tweet['in_reply_to_status_id'];
		$this->in_reply_to_status_id_str = $json_tweet['in_reply_to_status_id_str'];
		$this->in_reply_to_user_id = $json_tweet['in_reply_to_user_id'];
		$this->in_reply_to_user_id_str = $json_tweet['in_reply_to_user_id_str'];
		$this->lang = $json_tweet['lang'];
		$this->place = $json_tweet['place'];
		$this->possibly_sensitive = $json_tweet['possibly_sensitive'];
		$this->scopes = $json_tweet['scopes'];
		$this->retweet_count = $json_tweet['retweet_count'];
		$this->retweeted = $json_tweet['retweeted'];
		$this->retweeted_status = $json_tweet['retweeted_status'];
		$this->source = $json_tweet['source'];
		$this->text = $json_tweet['text'];
		$this->truncated = $json_tweet['truncated'];
		$this->user = $json_tweet['user'];
		$this->withheld_copyright = $json_tweet['withheld_copyright'];
		$this->withheld_in_countries = $json_tweet['withheld_in_countries'];
		$this->withheld_scope = $json_tweet['withheld_scope'];
	}

	/*
	 * returns https version of tweet profile image
	 */
	public function getProfileImageUrl() {
		echo $this->user['profile_image_url'];
		return $this->user['profile_image_url'];
	}

	public function getInfo() {

		return array(
			'name' => $this->user['name'],
			'screen_name' => $this->user['screen_name'],
			'text' => $this->text,
			'date' => $this->getRelativeDate(),
			'image_url' => $this->user['profile_image_url']
		);
	}

	public function getRelativeDate() {

		$now = new \DateTime();
		$then = new \DateTime($this->created_at);
		$then->setTimezone('America/Denver');

		$interval = $now->diff($then);
		return \misc\Util::IntervalToPostTime($interval);
	}

	public function getHTML($context, $vars) {

	}
}

?>
