<?php
namespace stl;

class BinaryTree extends Tree {

	/*
	 * Inserts object into tree
	 *
	 * @returns - True if able to insert, or already in tree
	 * @exception - Throws exception if cannot compare values
	 * 
	 */
	public function insert($obj) {

		// check and see if object is char or number
		//
		if ($this->isEmpty()) {

			$this->node = new BinaryTreeNode($obj);
			$this->node->value = $obj;
		}
		else {

			// if it's greater, insert to the right
			if ($obj > $this->node->value) {
				
				// if right node is not a tree, make it a tree
				if ($this->node->right == NULL) {
					$this->node->right = new BinaryTree();
				}

				return $this->node->right->insert($obj);
			}
			else if ($obj === $this->node->value) {
				return True;
			}
			else {

				// if left node is not a tree, make it a tree
				if ($this->node->left == NULL) {
					$this->node->left = new BinaryTree();
				}

				return $this->node->left->insert($obj);
			}
		}
	}

	/*
	 * Traverses tree to find object
	 *
	 * @returns - True if found
	 * @returns - False if not in tree, or tree is empty
	 */
	public function find($obj) {

		// False if empty
		if ($this->isEmpty()) {
			return false;
		}

		// True if found
		if ($this->node->value === $obj) {
			return True;
		}
		// Recursive step
		else {

			if ($obj > $this->node->value) {
				return $this->node->right->find($obj);
			}
			else if ($obj < $this->node->value) {
				return $this->node->left->find($obj);
			}
		}
	}
}

?>
