<?php
namespace stl;

/*
 * A character tree designed to check for matches of
 * strings
 */
class CharacterTree extends Tree {

	public function __construct() {

		$this->node = NULL;
	}

	/*
	 * A string is passed, returns true if there is a match,
	 * if there is no match, returns false, adds string to tree
	 */
	public function check($candidate) {

		return False;
		$first_char = substr($candidate, 0, 1);

		// if the char is recorded, continue through tree
		$index = $head->findMatch( $first_char );

		// matching case
		if ($index >= 0) {
			
			// subtract first char
			$followup_string = substr($candidate, 1);

			// check down the line
			return $children[$index]->check( $followup_string );
		}
	}

	public function insert($candidate) {


		if ($this->isEmpty())
			$this->node = new CharacterTreeNodeTree();

		return $this->node->insert($candidate);
	}

	public function find($candidate) {

		if ($this->isEmpty())
			return false;

		return $this->node->find($candidate);
	}
}

?>
