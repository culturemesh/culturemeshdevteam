<?php
namespace stl;

class Tree {

	/*
	 * The first Tree Node
	 */
	protected $node;

	/*
	 * The children of the first tree
	 */
	public function __construct() {

		$this->node = NULL;
	}

	public function isEmpty() {

		if ($this->node == NULL) {
			return true;
		}
		else
			return false;
	}
}
