<?php
namespace stl;

class CharacterTreeNodeTree extends BinaryTree {

	/*
	 * Inserts object into tree
	 *
	 * @returns - True if able to insert, or already in tree
	 * @exception - Throws exception if cannot compare values
	 * 
	 */
	public function insert($candidate) {


		/*
		 * If string is length 1
		 */
		if (strlen($candidate) == 1) {

			// Hasn't been given a node yet, rectify
			if ($this->isEmpty()) {

				$this->node = new CharacterTreeNode();
				$this->node->value = $candidate;
				$this->node->next = NULL;
				return True;
			}
			// Otherwise....
			else {

				// This thing is already in the tree
				//
				if ($candidate == $this->node->value) {

					// If it has another tree next to it...
					// mark it as a terminal
					if ($this->node->next != NULL) {

						$this->node->terminal = True;
						return True;
					}
				}
				// Or this thing can be found in a left or right branch
				//
				else if ($candidate > $this->node->value) {

					if ($this->node->right == NULL) {
						$this->node->right = new CharacterTreeNodeTree();
					}
					
					return $this->node->right->insert( $candidate );
				}
				else if ($candidate < $this->node->value) {

					if ($this->node->left == NULL) {
						$this->node->left = new CharacterTreeNodeTree();
					}
					
					return $this->node->left->insert( $candidate );
				}
			}
		}

		// If string is longer than 1 char
		// we're gonna have to chip away at it
		//
		if (strlen($candidate) > 1) {

			$first_char = substr($candidate, 0, 1);
			$last_part = substr($candidate, 1);

			// check and see if object is char or number
			//
			// We've reached a leaf, turn this into a new character tree
			if ($this->isEmpty()) {

				$this->node = new CharacterTreeNode($obj);
				$this->node->value = $first_char;

				// make a new tree for the next character
				$this->node->next = new CharacterTree();
				return $this->node->next->insert($last_part);
			}
			else {

				// we've encountered a matching character tree
				// time to move to the next letter
				if ($first_char === $this->node->value) {
					
					return $this->node->next->insert($last_part);
				}

				// if it's greater, insert to the right
				// keep it part of the binary tree
				if ($first_char > $this->node->value) {
					
					// if right node is not a tree, make it a tree
					if ($this->node->right == NULL) {
						$this->node->right = new CharacterTreeNodeTree();
					}

					return $this->node->right->insert($candidate);
				}
				if ($first_char < $this->node->value) {

					// if left node is not a tree, make it a tree
					if ($this->node->left == NULL) {
						$this->node->left = new CharacterTreeNodeTree();
					}

					return $this->node->left->insert($candidate);
				}
			}
		}
	}

	/*
	 * Searches binary tree for character
	 *
	 * When character is found, try to access the next tree
	 *
	 * @returns - True if character is found AND (at a leaf OR terminal)
	 * @returns - False if at a leaf and no match
	 */
	public function find($candidate) {

		if ($this->isEmpty())
			return False;

		// If string is empty
		//
		if (strlen( $candidate ) === 1) {

			if ($this->node->value === $candidate) {
				return True;
			}
			else {
				return False;
			}
		}

		// If ain't empty
		//
		else if (strlen( $candidate ) > 1) {

			$first_char = substr($candidate, 0, 1);
			$the_rest = substr($candidate, 1);

			if ($first_char > $this->node->value) {

				if ($this->node->right != NULL) {
					return $this->node->right->find( $candidate );
				}
				else {
					return False;
				}
			}
			else if ($first_char < $this->node->value) {

				if ($this->node->left != NULL) {
					return $this->node->left->find( $candidate );
				}
				else {
					return False;
				}
			}
			else {
				if ($this->node->next != NULL) {
					return $this->node->next->find( $the_rest );
				}
				else {
					return False;
				}
			}
		}
	}
}

?>
