<?php
namespace stl;

class BinaryTreeNode extends TreeNode {

	public $value;
	public $left;
	public $right;

	public function __construct() {

		$this->value = null;
		$this->left = null;
		$this->right = null;
	}
}
