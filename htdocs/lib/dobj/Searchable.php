<?php
namespace dobj;

class Searchable extends DObj {

	protected $name;
	protected $tweet_terms;
	protected $tweet_terms_override;

	public function toString() {
		return $name;
	}

	public static function getAll($cm, $dal, $do2db)  {

	}
}

?>
