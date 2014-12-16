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

	public function getHTML($context) {

	}
}
