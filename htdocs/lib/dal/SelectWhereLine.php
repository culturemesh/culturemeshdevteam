<?php
namespace dal;

class SelectWhereLine {

	private $column;
	private $operator;
	private $conjunction;
	private $value;
	private $value_count;
	private $question_mark = '?';

	private $no_space_operators;

	/*
	 *
	 */
	public function __construct($column, $operator, $conjunction=NULL, $value=NULL, $type=NULL, $value_count=NULL, $prefix=NULL) {

		$this->column = $column;
		$this->prefix = $prefix;
		$this->operator = $operator;
		$this->conjunction = $conjunction;
		$this->value = $value;
		$this->type = $type;
		$this->value_count = $value_count;

		$this->no_space_operators = array('=', '>=', '<=', '<>');
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
		if (!in_array($formatted_operator, $this->no_space_operators) && $formatted_operator !== 'IS NULL') {
			$formatted_operator = ' ' . $formatted_operator . ' ';
		}

		if ($formatted_operator === 'IS NULL') {
			$formatted_operator = ' ' . $formatted_operator;
		}

		// put together most of the return string
		$return_string = $this->prefix . $this->column . $formatted_operator;

	       
		// Add single question mark for most things, but not: 
		//
		// 	Null operators
		// 	IN operators
		//
		if (!in_array($this->operator, array('IN', 'NOT IN', 'IS NULL'))) {
			$return_string .= $this->question_mark;
		}
		else if (in_array($this->operator, array('IN', 'NOT IN'))) {

			if ($this->value_count == NULL)
				throw new \Exception('SelectWhereLine: Using an IN operator without supplying a value count.');

			$question_mark_string = '?';

			for ($i = 0; $i < ($this->value_count - 1); $i++) {
				$question_mark_string .= ', ?';
			}

			$return_string .= '(' . $question_mark_string . ')';
		}

		// Add conjunction to the string if
		// it's present
		//
		if ($this->conjunction != NULL) {
			$return_string = ' ' . $this->conjunction . ' ' . $return_string;
		}

		return $return_string;
	}

	/*
	 * Tells whether select where is a NULL
	 *
	 */
	public function isNull() {
		if ($this->operator == 'IS NULL')
			return True;
		else
			return False;
	}

	public function getColumn() {
		return $this->column;
	}

	public function getParamColumns() {
		if ($this->value_count === NULL)
			return $this->column;
		else {
			$column_instances = array();

			for ($i = 0; $i < $this->value_count; $i++) {
				array_push($column_instances, $this->column);
			}

			return $column_instances;
		}
	}

	public function getValue() {
		return $this->value;
	}

	public function getType() {
		return $this->type;
	}

	public function getConjunction() {
		return $this->conjunction;
	}
}

?>
