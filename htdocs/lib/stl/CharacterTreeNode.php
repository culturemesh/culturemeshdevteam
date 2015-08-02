<?php
namespace stl;

class CharacterTreeNode extends TreeNode {

	/*
	 * A terminal value if it has children, but
	 * had a shorter string inserted
	 */
	public $terminal;

	public function __construct() {

		$this->terminal = False;
	}

	public function hasValue() {

		if ($this->value == NULL)
			return False;
		else
			return True;
	}
}
