<?php
namespace dobj;

class TweetReply extends Reply {

	/*
	 * Inserts a reply to a tweet into the db
	 *
	 * Params:
	 * 	dal - a data access layer object
	 * 	do2db - translation from db to dobj
	 *
	 * Returns:
	 * 	(int) id - The id of the reply
	 */
	public function insert($dal, $do2db) {

		if (!isset($this->reply_text))
			throw new \Exception('No text has been set');
		if (!isset($this->id_parent))
			throw new \Exception('No parent has been set');
		if (!isset($this->id_network))
			throw new \Exception('No network has been set');
		if (!isset($this->id_user))
			throw new \Exception('No user has been set');

		$result = $do2db->execute($dal, $this, 'insertTweetReply');
		return $dal->lastInsertId();
	}

	public function delete($dal, $do2db) {

		if(!isset($this->id))
			throw new \Exception('No id has been set');

		$result = $do2db->execute($dal, $this, 'deleteTweetReply');

		if ($result[0] !== "00000") {
			echo $result[2];
		}
		else {
			return true;
		}
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
				'vars' => $cm->getVars(),
				'tweet' => True
				)
			);
				
			break;
		}
	}
}
