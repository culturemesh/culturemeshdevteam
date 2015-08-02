<?php
namespace stl;

class TreeNode {

	/*
	 * Pointer to the
	 */
	public $next;

	/*
	 * Data that we're lookin' for
	 */
	public $value;

	public function __construct($item=NULL) {

		if ($item != NULL) {
			$this->value = $item;
		}
	}
}

?>
