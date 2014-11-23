<?php
namespace dobj;

class Conversation extends DisplayDObj {

	protected $start_date;
	private $post;
	private $replies;

	public function __construct() {

	}

	public static function createFromResult() {

		return new Conversation();
	}

	public function start_date($value=NULL) {

		if ($value == NULL)
			return $this->last_login;

		if (!strtotime($value)) {
			throw new \InvalidArgumentException('Error: start_date must be a string');
			return false;
		}

		$this->last_login = $value;

		return true;
	}

	/*
	 * must implements
	 */
	public static function createFromId($id) {

		return new Conversation();
	}

	public function display($context) {

	}

	public function getHTML($context) {

	}
}
