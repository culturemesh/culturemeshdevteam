<?php
namespace dal;

class CustomSelectQuery extends DBQuery {

	private $query_values;

	// query values
	private $select_intro = 'SELECT ';
	private $from_intro = ' FROM ';
	private $where_intro = ' WHERE ';
	private $group_by_intro = ' GROUP BY ';
	private $order_by_intro = ' ORDER BY ';
	private $limit_intro = ' LIMIT ';

	private $asterisk = '*';

	private $where_lines;
	private $where_values;
	private $where_value_types;

	public function __construct() {

	}

	public function setValues($user_values) {

		$this->query_values = array_merge(array(
			'name' => 'default',
			'select_rows' => array(),
			'from_tables' => array(),
			'where_cols' => array(),
			'where_values' => array(),
			'where_operators' => '=',
			'where_conjunctions' => 'AND',
			'value_types' => '',
			'order_by_table' => NULL,
			'order' => NULL, // defaults to ASC
			'group_by_table' => NULL,
			'limit_offset' => NULL,
			'limit_row_count' => NULL,
			'returning_class' => 'dobj\Blank'
		),
	       	$user_values);
	}

	public function getValues() {

		return $this->query_values;
	}

	/*
	 * A basic sql query
	 *
	 * Restrictions:
	 * 	- no joins
	 * 	- no nested selects
	 * 	- no aliases
	 * 	- no limits
	 */
	public function writeQueryString() {

		$query = '';

		if (count($this->query_values['select_rows']) == 0) {
			$query .= $this->select_intro . $this->asterisk;
		}
		else {
			$query .= $this->select_intro . join(', ', $this->query_values['select_rows']);
		}

		$query .= $this->from_intro . join(', ', $this->query_values['from_tables']);

		// CREATE WHERE CLAUSE
		$where_clause = '';

		for($i = 0; $i < count($this->where_lines); $i++) {

			// get string
			$line = $this->where_lines[$i]->toString();

			// add where if we're on the first thing
			if ($i == 0) {
				$line = $this->where_intro . $line;
			}

			$where_clause .= $line;
		}	

		$query .= $where_clause;

		// ADD ORDER BY CLAUSE
		if ($this->query_values['order_by_table'] != NULL) {
			
			$order_by_clause = '';

			if (is_array($this->query_values['order_by_table'])) {
				$query .= $this->order_by_intro . join(', ', $this->query_values['order_by_table']);
			}
			else {
				$query .= $this->order_by_intro . $this->query_values['order_by_table'];
			}

			if ($this->query_values['order'] == NULL) {
			  $order_by_clause .= $this->query_values['order'];
			}

			$query .= $order_by_clause;
		}

		// ADD GROUP BY CLAUSE
		if ($this->query_values['group_by_table'] != NULL) {

			if (is_array($this->query_values['group_by_table'])) {
				$query .= $this->group_by_intro . join(', ', $this->query_values['group_by_table']);
			}
			else {
				$query .= $this->group_by_intro . $this->query_values['group_by_table'];
			}
		}

		// ADD LIMIT STRING
		if ($this->query_values['limit_offset'] != NULL) {

			$limit_string = '';

			// add offset
			$limit_string .= $this->limit_intro . $this->query_values['limit_offset'];

			if ($this->query_values['limit_row_count'] != NULL) {
				$limit_string .= ',' . $this->query_values['limit_row_count'];
			}

			$query .= $limit_string;
		}

		return $query;
	}

	public function toDBQuery($con=NULL) {

		$db_query = new DBQuery();
		$db_query->setValues(array(
			'query' => $this->writeQueryString(),
			'test_query' => NULL,
			'name' => $this->query_values['name'],
			'params' => $this->where_columns,
			'nullable' => array(),
			'returning' => True,
			'returning_list' => True,
			'returning_value' => False,
			'returning_assoc' => False,
			'returning_class' => $this->query_values['returning_class'],
			'returning_cols' => array()
		));

		$db_query->setConnection($con);
		return $db_query;
	}

	public function addAWhere($column, $operator='=', $value, $type='s') {

		$this->where_lines = array();
		$this->where_columns = array();
		$this->param_obj = new \dobj\Blank();
		$this->where_value_types = '';

		array_push($this->where_lines, new SelectWhereLine($column, $operator));
		array_push($this->where_columns, $column);
		$this->param_obj->$column = $value;
		$this->where_value_types .= $type;

		return True;
	}

	public function addAnotherWhere($conjunction='AND', $column, $operator='=', $value, $type='s') {

		if ($this->where_lines == NULL || $this->param_obj == NULL || $this->where_value_types == NULL) {
			throw new \Exception('CustomSelectQuery->addAnotherWhere: No first query has been set');
		}

		array_push($this->where_lines, new SelectWhereLine($column, $operator, $conjunction));

		// add array to columns list
		if (!in_array($column, $this->where_columns)) {
			array_push($this->where_columns, $column);
		}

		// If there are multiple arguments for one column,
		// we must make adjustments. 
		//
		// An array will contain multiple arguments for one column
		//
		if ($this->param_obj->$column != NULL) {

			if (is_array($this->param_obj->$column)) {
				array_push($this->param_obj->$column, $value);
			}
			else {
				$placeholder = $this->param_obj->$column;
				$this->param_obj->$column = array();

				// push things
				array_push($this->param_obj->$column, $placeholder);
				array_push($this->param_obj->$column, $value);
			}
		}
		// The normal situation, one column, one parameter
		else {
			$this->param_obj->$column = $value;
		}

		$this->where_value_types .= $type;

		return True;
	}

	public function getParamObject() {
		return $this->param_obj;
	}
}

?>
