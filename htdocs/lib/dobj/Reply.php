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
			$owner = false;
			$active = false;
			if (isset($_SESSION['uid'])) {
				$active = true;

				if ($this->id_user == $_SESSION['uid']) {
					$owner = true;
				}
			}

			// get template
			$template = file_get_contents($cm->template_dir . $cm->ds . 'network-reply.html');
			return $mustache->render($template, array(
				'active' => $active,
				'reply' => $this->prepare($cm),
				'text' => $this->formatText(),
				'owner' => $owner,
				'relative_date' => $this->getRelativeDate(),
				'name' => $this->getName(),
				'replies' => $this->replies_html,
				'vars' => $cm->getVars()
				)
			);
				
			break;
		}
	}

	protected function getText() {
		return $this->reply_text;
	}

	protected function getRelativeDate() {
		$now = new \DateTime();
		$then = new \DateTime($this->reply_date);

		$interval = $now->diff($then);
		return \misc\Util::IntervalToPostTime($interval);
	}

	public function insert($dal, $do2db) {

		if (!isset($this->reply_text))
			throw new \Exception('No text has been set');
		if (!isset($this->id_parent))
			throw new \Exception('No parent has been set');
		if (!isset($this->id_network))
			throw new \Exception('No network has been set');
		if (!isset($this->id_user))
			throw new \Exception('No user has been set');

		$result = $do2db->execute($dal, $this, 'createReply');
		return $dal->lastInsertId();
	}

	public function delete($dal, $do2db) {

		if(!isset($this->id))
			throw new \Exception('No id has been set');

		$result = $do2db->execute($dal, $this, 'deleteReply');

		if ($result[0] !== "00000") {
			echo $result[2];
		}
		else {
			return true;
		}
	}
}
