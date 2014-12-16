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

	private static $MAX_IMAGE_COUNT = 3;
	protected $replies;

	public static function createFromId($id, $dal, $do2db) {

		// stub
	}

	public function display($context) {

	}

	public function getHTML($context) {

	}

	public function getReplies($dal, $do2db) {

		if ($this->id == NULL)
			throw \Exception('This post does not have an id, can\'t find replies');

		$this->replies = $do2db->execute($dal, $this, 'getRepliesByParentId');
	}
}

?>
