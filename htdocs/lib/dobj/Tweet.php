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

	private static $MAX_REPLIES = 4;

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

		$cm = $vars['cm'];
		$mustache = $vars['mustache'];

		$network = $vars['network'];

		// activate replies_html
		$this->replies_html = array();

		$show_replies = false;
		if (count($this->replies) > self::$MAX_REPLIES) {
			$show_replies = true;
		}

		// get html for replies
		for ($i = 0; $i < count($this->replies) && $i < self::$MAX_REPLIES; $i++) {

			$reply = $this->replies[$i];
			$html = $reply->getHTML($context, $vars);
			array_push($this->replies_html, $html);
		}

		// get name
		$name = $this->getName();

		// check authentication
		$delete_button = false;
		$reply_request = false;

		$site_user = NULL;
		if (isset($_SESSION['uid'])) {
			$active = true;
			$site_user = $vars['site_user'];

			// if we're making a new post
			// --- give own switch statement later
			if ($name == 'UNNAMED USER') {
				$name = $site_user->getName();
				$this->img_link = $site_user->img_link;
			}

			$reply_request = $site_user->checkNetworkRegistration($network->id);

			if ($this->id_user == $site_user->id) {
				$delete_button = true;
			}
		}

		// get template
		$template = file_get_contents($cm->template_dir . $cm->ds . 'network-tweet.html');
		return $mustache->render($template, array(
			'active' => true,
			'delete_button' => $delete_button,
			'reply_request' => $reply_request,
			'show_replies' => $show_replies,
			'tweet' => $this->getInfo(),
			'site_user' => $site_user,
			'replies' => $this->replies_html,
			'vars' => $cm->getVars()
			)
		);
	}
}

?>
