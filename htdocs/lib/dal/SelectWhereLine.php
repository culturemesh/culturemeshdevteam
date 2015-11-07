<?php
namespace dal;

class SelectWhereLine {

	private $column;
	private $operator;
	private $conjunction;
	private $question_mark = '?';

	private $no_space_operators;

	/*
	 *
	 */
	public function __construct($column, $operator, $conjunction=NULL) {

		$this->column = $column;
		$this->operator = $operator;
		$this->conjunction = $conjunction;

		$no_space_operators = array('=', '>=', '<=', '<>');
	}

	/*
	 *
	 */
	public function toString() {

		$return_string = NULL;
		$formatted_operator = $this->operator;

		// add a space if we're dealing with
		//  non symbol based operators like LIKE or NOT IN
		//
		if (!in_array($formatted_operator, $no_space_operators)) {
			$formatted_operator = ' ' . $formatted_operator . ' ';
		}

		// put together most of the return string
		$return_string = $this->column . $formatted_operator;
	       
		// Add question mark for most things, but not null operators
		if ($this->operator !== 'IS NULL')
			$return_string .= $this->question_mark;

		// Add conjunction to the string if
		// it's present
		//
		if ($this->conjunction != NULL) {
			$return_string = ' ' . $this->conjunction . ' ' . $return_string;
		}

		return $return_string;
	}
}

?>
