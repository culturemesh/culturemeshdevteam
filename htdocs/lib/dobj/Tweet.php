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

	// transplanting from the user variable for
	// now
	protected $name;
	protected $screen_name;
	protected $profile_image_url;

	protected $saved; // how we distinguish db tweets from api tweets

	private static $MAX_REPLIES = 4;

	/*
	 * Your standard Create From Id function
	 *
	 */
	public static function createFromId($id, $dal, $do2db) {

		$obj = new \dobj\Blank();
		$obj->id = $id;

		$result = $do2db->execute($dal, $obj, 'getTweetById');

		if (get_class($result) == 'PDOStatement')
			return false;
		else
			return $result;
	}

	/*
	 * Your standard insert function
	 *
	 */
	public function insert($dal, $do2db) {

		if (!isset($this->id))
			throw new \Exception('Tweet: id is not set');
		if (!isset($this->id_network))
			throw new \Exception('Tweet: id_network is not set');
		if (!isset($this->text))
			throw new \Exception('Tweet: text is not set');
		if (!isset($this->name))
			throw new \Exception('Tweet: name is not set');
		if (!isset($this->screen_name))
			throw new \Exception('Tweet: screen_name is not set');
		if (!isset($this->profile_image_url))
			throw new \Exception('Tweet: profile_image_url is not set');
		if (!isset($this->created_at))
			throw new \Exception('Tweet: created_at is not set');

		$do2db->execute($dal, $this, 'insertPostTweet');
	}


	public function delete($dal, $do2db) {

		if(!isset($this->id))
			throw new \Exception('No id has been set');

		$result = $do2db->execute($dal, $this, 'deleteTweet');

		if ($result[0] !== "00000") {
			echo $result[2];
		}
		else {
			return true;
		}
	}

	/*
	 *  Gets a list of the replies to this here tweet
	 *
	 */
	public function getReplies($dal, $do2db) {

		if (!isset($this->id))
			throw new \Exception('Tweet: id is not set');

		$result = $do2db->execute($dal, $this, 'getTweetRepliesByParentId');

		if (get_class($result) == 'PDOStatement') {
			$this->replies = new DOBjList();
		}
		else
			$this->replies = $result;
	}

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

		// extra stuff
		$this->name = $this->user->name;
		$this->screen_name = $this->user->screen_name;
		$this->profile_image_url = $this->user->profile_image_url;
	}

	/*
	 * returns https version of tweet profile image
	 */
	public function getProfileImageUrl() {
		return $this->user['profile_image_url'];
	}

	public function getInfo() {

		$name = $this->user['name'];
		$screen_name = $this->user['screen_name'];
		$profile_image_url = $this->getProfileImageUrl();

		if (isset($this->name))
			$name = $this->name;
		if (isset($this->screen_name))
			$screen_name = $this->screen_name;
		if (isset($this->profile_image_url))
			$profile_image_url = $this->profile_image_url;

		return array(
			'id' => $this->id,
			'name' => $name,
			'screen_name' => $screen_name,
			'text' => $this->text,
			'date' => $this->getRelativeDate(),
			'timestamp' => $this->created_at,
			'profile_image' => $profile_image_url
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

		switch($context) {

		case 'network':

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
				'network' => $network,
				'delete_button' => $delete_button,
				'reply_request' => $reply_request,
				'show_replies' => $show_replies,
				'tweet' => $this->getInfo(),
				'site_user' => $site_user,
				'replies' => $this->replies_html,
				'vars' => $cm->getVars()
				)
			);

			break;

		case 'replies':

			// activate replies_html
			$this->replies_html = array();

			// get html for replies
			foreach ($this->replies as $reply) {
				$html = $reply->getHTML('network', $vars);
				array_push($this->replies_html, $html);
			}

			return $this->replies_html;

			break;
		}
	}
}

?>
