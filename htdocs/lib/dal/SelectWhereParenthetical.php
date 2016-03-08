<?php
namespace dal;

class SelectWhereParenthetical {

	private $lines;
	private $conjunction;
	private $type_string;

	private $opening = '(';
	private $closing = ')';

	public function __construct($lines, $conjunction=NULL) {

		$this->lines = $lines;
		$this->conjunction=$conjunction;
	}

	public function toString() {

		$return_string = '';

		if ($this->conjunction != NULL)
		  $return_string .= ' ' . $this->conjunction . ' ';

		// add opening
		$return_string .= $this->opening;

		foreach($this->lines as $line) {
			$return_string .= $line->toString();
		}

		// add closing
		$return_string .= $this->closing;

		return $return_string;
	}

	/*
	 * Returns the type strings of all the Select lines
	 * in the parenthetical
	 *
	 */
	public function getTypeString() {

		$type_string = '';
		
		foreach ($this->lines as $line) {

			if ($line->type != NULL)
			  $type_string .= $line->type;
		}

		return $type_string;
	}

	public function getColumns() {

		$columns = array();

		foreach($this->lines as $line) {
			if ($line->getValue() !== NULL) {
			  array_push($columns, $line->getColumn());
			}
		}

		return $columns;
	}

	public function getParamColumns() {

		$columns = array();

		foreach($this->lines as $line) {
			if ($line->getValue() !== NULL) {

				$param_columns = $line->getParamColumns();

				if (is_array($param_columns))
				  $columns = array_merge($columns, $param_columns);
				else
				  array_push($columns, $line->getColumn());
			}
		}

		return $columns;
	}

	public function getAssociativeValues() {

		$params = array();

		foreach ($this->lines as $line) {
			$column = $line->getColumn();
			$params[$column] = $line->getValue();
		}

		return $params;
	}
}

?>
