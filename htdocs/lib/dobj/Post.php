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

	protected $images;
	protected $image_ids;

	protected $date;

	private static $MAX_IMAGE_COUNT = 3;
	protected $replies;
	protected $replies_html;

	public static function createFromId($id, $dal, $do2db) {

		// stub
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

	public function registerImages($dal, $do2db) {

		$obj = new \dobj\Blank();
		$obj->id_post = $this->id;

		var_dump($obj);

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

			// get html for replies
			foreach ($this->replies as $reply) {
				$html = $reply->getHTML($context, $vars);
				array_push($this->replies_html, $html);
			}

			// check authentication
			if (isset($_SESSION['uid'])) {
				$active = true;

				if ($this->id_user == $_SESSION['uid']) {
					$delete_button = true;
				}
			}

			// get template
			$template = file_get_contents($cm->template_dir . $cm->ds . 'network-post.html');
			return $mustache->render($template, array(
				'active' => true,
				'post' => $this,
				'text' => $this->formatText(),
				'relative_date' => $this->getRelativeDate(),
				'name' => $this->getName(),
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
				'post' => $this,
				'relative_date' => $this->getRelativeDate(),
				'name' => $this->getName(),
				)
			);
			break;
		}
	}

	public function getImages() {
		$hashes = explode(', ', $this->hash);
		$this->images = array();

		foreach ($hashes as $hash) {
			$img = new \dobj\Image();
			$img->hash = $hash;
			$img->post = 1;
			array_push($this->images, $img);
		}
	}

	public function getImagePaths() {

		$dirs = array();

		foreach ($this->images as $image) 
			array_push($dirs, $image->getPathAndName('post'));

		return $dirs;
	}

	public function getReplies($dal, $do2db) {

		if ($this->id == NULL)
			throw \Exception('This post does not have an id, can\'t find replies');

		$this->replies = $do2db->execute($dal, $this, 'getRepliesByParentId');
	}

	protected function getRelativeDate() {
		$now = new \DateTime();
		$then = new \DateTime($this->post_date);

		$interval = $now->diff($then);
		return \misc\Util::IntervalToPostTime($interval);
	}

	// parse name
	protected function getName() {

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

	private function formatText() {

		$raw_text = $this->post_text;
		return $raw_text;
	}
}

?>
