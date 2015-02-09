<?php
namespace dobj;

class Post extends DisplayDObj {

	protected $id_user;
	protected $id_network;
	protected $post_date;
	protected $post_text;
	protected $post_class;
	protected $post_original;
	protected $email;
	protected $username;
	protected $first_name;
	protected $last_name;
	protected $img_link;
	protected $reply_count;
	protected $hash;

	// network
	protected $network;
	protected $network_class;
	protected $city_cur;
	protected $region_cur;
	protected $country_cur;
	protected $city_origin;
	protected $region_origin;
	protected $country_origin;
	protected $language_origin;
	protected $origin;
	protected $location;

	// images
	protected $images;
	protected $image_ids;

	protected $date;

	private static $MAX_IMAGE_COUNT = 3;
	private static $MAX_REPLIES = 4;
	protected $replies;
	protected $replies_html;

	public static function createFromId($id, $dal, $do2db) {

		$obj = new \dobj\Blank();
		$obj->id = $id;

		$result = $do2db->execute($dal, $obj, 'getPostById');

		if (get_class($result) == 'PDOStatement')
			return false;
		else
			return $result;
	}

	public function insert($dal, $do2db) {

		if (!isset($this->id_user))
			throw new \Exception('id_user is not set');
		if (!isset($this->id_network))
			throw new \Exception('id_network is not set');
		if (!isset($this->post_text))
			throw new \Exception('post_text is not set');
		if (!isset($this->post_class))
			throw new \Exception('post_class is not set');

		$do2db->execute($dal, $this, 'insertPost');
	}

	public function delete($dal, $do2db) {
		if (!isset($this->id))
	  	  throw new \Exception('id is not set');

		$result = $do2db->execute($dal, $this, 'deletePost');

		if (get_class($result) == 'PDOStatement') {
			var_dump($result->getErrorInfo());
		}
		else
		  return $result;
	}

	public function registerImages($dal, $do2db) {

		$obj = new \dobj\Blank();
		$obj->id_post = $this->id;

		for ($i = 0; $i < 3; $i++) {
			$varname = 'id_image'.($i+1);
			if (isset($this->image_ids[$i]))
				$obj->$varname = $this->image_ids[$i];
			else
				$obj->$varname = NULL;
		}

		$do2db->execute($dal, $obj, 'registerPostImage');
	}

	public function display($context) {

	}

	public function getHTML($context, $vars) {

		// get vars
		$cm = $vars['cm'];
		$mustache = $vars['mustache'];

		// calculate date

		switch($context) {

		case 'network':

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
			$template = file_get_contents($cm->template_dir . $cm->ds . 'network-post.html');
			return $mustache->render($template, array(
				'active' => true,
				'delete_button' => $delete_button,
				'reply_request' => $reply_request,
				'show_replies' => $show_replies,
				'post' => $this->prepare($cm),
				'text' => $this->formatText(),
				'relative_date' => $this->getRelativeDate(),
				'name' => $name,
				'site_user' => $site_user,
				'replies' => $this->replies_html,
				'images' => $this->getImagePaths(),
				'vars' => $cm->getVars()
				)
			);
				
			break;

		case 'dashboard':

			// get template
			$template = file_get_contents($cm->template_dir . $cm->ds . 'dashboard-post.html');
			return $mustache->render($template, array(
				'active' => true,
				'post' => $this->prepare($cm),
				'relative_date' => $this->getRelativeDate(),
				'name' => $this->getName(),
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
		}
	}

	public function getImages() {
		$hashes = explode(', ', $this->hash);
		$this->images = array();

		if (count($hashes) < 2 && $hashes[0] == '')
			$hashes = NULL;
		else {
			foreach ($hashes as $hash) {

				$img = new \dobj\Image();
				$img->hash = $hash;
				$img->post = 1;
				array_push($this->images, $img);
			}
		}
	}

	public function getImagePaths() {

		$dirs = array();

		if ($this->images == NULL) {
			$this->getImages();
		}

		foreach ($this->images as $image) 
			array_push($dirs, $image->getPathAndName('post'));

		return $dirs;
	}

	public function getReplies($dal, $do2db) {

		if ($this->id == NULL)
			throw new \Exception('This post does not have an id, can\'t find replies');

		$result = $do2db->execute($dal, $this, 'getRepliesByParentId');

		if (get_class($result) == 'PDOStatement') {
			$this->replies = NULL;
		}
		else 
		  $this->replies = $result;
	}

	protected function getRelativeDate() {
		$now = new \DateTime();
		$then = new \DateTime($this->post_date);

		$interval = $now->diff($then);
		return \misc\Util::IntervalToPostTime($interval);
	}

	// parse name
	public function getName() {

		$name = NULL;
		if ($this->first_name == '')
			$name = "UNNAMED USER";
		else {
			$name = $this->first_name;
			if (isset($this->last_name))
				$name .= " ".$this->last_name;
		}

		return $name;
	}

	public function getSplit($property) {

		switch ($property) {
		case 'network':
			return $this->getNetworkTitle();
		default:
			return $this->$property;
		}
	}

	protected function getText() {
		return $this->post_text;
	}

	public function prepare($cm) {

		if (!isset($cm))
			throw new \Exception('No environment variable passed to user');

		// get image thing set up
		if ( !is_file($cm->img_repo_dir . $cm->ds . $this->img_link)) 
		  $this->img_link = '//' . $cm->hostname . $cm->ds . 'images/blank_profile.png';
		else
		  $this->img_link = $cm->img_host_repo . '/' . $this->img_link;

		return $this;
	}

	public function formatText() {

		// split on links
		$raw_text = $this->getText();
		$all_chars = ".+";

		// remove link tags
		$no_ltag = \misc\Util::StrExtract($raw_text, 'link');

		// autodetect links w/o tags
		$al_match = "#((?:http|https|ftp)\:\/\/)*([a-zA-Z0-9]+\.[a-zA-Z0-9.]+)([\/a-zA-Z0-9\?\+\%\&\.\-\#\=\_]*)#";
		//$al_match = "#((?:http|https|ftp)\:\/\/)*({[a-zA-Z0-9]+\.}+[a-zA-Z0-9]+)([\/a-zA-Z0-9\?\+\%\&\.\-\#\=\_]*)#";
		$al_replace = '<a target=\'_blank\' href=\'http://${2}${3}\'>${1}${2}${3}</a>';
		$new_text = preg_replace($al_match, $al_replace, $no_ltag['replacement']);

		// replace link tags
		$new_text = \misc\Util::StrReform($new_text, 'link', $no_ltag['extractions']);

		// find bolded text
		$match = "#\[b\](".$all_chars.")\[/b\]#";
		$replacement = '<b>${1}</b>';

		$new_text = preg_replace($match, $replacement, $new_text);

		// find italicized text
		$match = "#\[i\](". $all_chars .")\[/i\]#";
		$replacement = '<i>${1}</i>';

		$new_text = preg_replace($match, $replacement, $new_text);

		// find links
		$match = "#\[link\]((?:http|https|ftp)\:\/\/)*(". $all_chars .")\[/link\]#";
		$replacement = '<a target=\'_blank\' href=\'http://${2}\'>${2}</a>';

		$new_text = preg_replace($match, $replacement, $new_text);

		return $new_text;
	}


}

?>
