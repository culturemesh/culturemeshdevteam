<?php
namespace dobj;

class Reply extends Post {

	protected $id_parent;
	protected $id_user;
	protected $id_network;
	protected $reply_date;
	protected $reply_text;
	protected $email;
	protected $username;
	protected $first_name;
	protected $last_name;
	protected $img_link;

	public static function createFromId($id, $dal, $do2db) {

		// stub
	}

	public function display($context) {

	}

	public function getHTML($context, $vars) {

		// get vars
		$cm = $vars['cm'];
		$network = $vars['network'];
		$mustache = $vars['mustache'];

		// calculate date

		switch($context) {

		case 'network':

			// check authentication
			if (isset($_SESSION['uid'])) {
				$active = true;

				if ($this->id_user == $_SESSION['uid']) {
					$delete_button = true;
				}
			}

			// get template
			$template = file_get_contents($cm->template_dir . $cm->ds . 'network-reply.html');
			return $mustache->render($template, array(
				'active' => true,
				'reply' => $this,
				'relative_date' => $this->getRelativeDate(),
				'name' => $this->getName(),
				'replies' => $this->replies_html,
				'vars' => $cm->getVars()
				)
			);
				
			break;
		}
	}

	protected function getRelativeDate() {
		$now = new \DateTime();
		$then = new \DateTime($this->reply_date);

		$interval = $now->diff($then);
		return \misc\Util::IntervalToPostTime($interval);
	}
}
