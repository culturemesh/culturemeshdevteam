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
			'returning_class' => 'dobj\Blank',
			'returning_list' => True,
			'params_stack' => True
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
			'params' => $this->getWhereColumns(),
			'params_stack' => $this->query_values['params_stack'],
			'nullable' => array(),
			'returning' => True,
			'returning_list' => $this->query_values['returning_list'],
			'returning_value' => False,
			'returning_assoc' => False,
			'returning_class' => $this->query_values['returning_class'],
			'returning_cols' => array()
		));

		$db_query->setConnection($con);
		return $db_query;
	}

	public function setInWhereColumns($arg) {

		if ($this->where_columns === NULL) {
			$this->where_columns = array();
		}

		// Add to where columns as array or as argument
		if (is_array($arg)) {
			$this->where_columns = array_merge($this->where_columns, $arg);
		}
		else {
			array_push($this->where_columns, $arg);
		}
	}

	public function setInParamObject($column, $value) {

		if ($this->param_obj === NULL) {
			$this->param_obj = new \dobj\Blank();
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

		return True;
	}

	/*
	 * Adds a line to a custom WHERE statement
	 */
	public function addAWhere($column, $operator='=', $value, $type='s', $value_count=NULL) {

		$this->where_lines = array();
		$this->where_value_types = '';

		$line = new SelectWhereLine($column, $operator, NULL, $value, $type, $value_count);
		array_push($this->where_lines, $line);

		$this->setInWhereColumns($line->getParamColumns());
		$this->setInParamObject($line->getColumn(), $line->getValue());
		$this->where_value_types .= $type;

		return True;
	}

	public function addAnotherWhere($conjunction='AND', $column, $operator='=', $value, $type='s', $value_count=NULL) {

		if ($this->where_lines == NULL || $this->param_obj == NULL || $this->where_value_types == NULL) {
			throw new \Exception('CustomSelectQuery->addAnotherWhere: No first query has been set');
		}

		$line = new SelectWhereLine($column, $operator, $conjunction, $value, $type, $value_count);
		array_push($this->where_lines, $line);

		/*
		// add array to columns list
		if (!in_array($column, $this->where_columns)) {
			array_push($this->where_columns, $column);
		}
		*/

		/*
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
			$this->addToParamObj($column, $value);
		}
		*/
		$this->setInWhereColumns($line->getParamColumns());
		$this->setInParamObject($line->getColumn(), $line->getValue());
		$this->where_value_types .= $type;

		return True;
	}

	public function createWhereLine($column, $operator='=', $conjunction=NULL, $value, $type=NULL, $value_count=NULL) {
		return new SelectWhereLine($column, $operator, $conjunction, $value, $type, $value_count);
	}

	public function insertWhereLine($line) {

		if (get_class($line) !== 'dal\SelectWhereLine') {
			throw new \Exception('CustomSelectQuery->insertWhereLine: valid where line not passed');
		}

		if ($this->where_lines == NULL)  {
			$this->where_lines = array();
		}

		/*
		if ($this->param_obj == NULL) {
			$this->param_obj = new \dobj\Blank();
		} 
		*/

		if ($this->where_value_types == NULL) {
			//$this->where_columns = array();
			$this->where_value_types = '';
		}

		if ($line->isNull()) {
			$this->insertANull($line);
			return True;		
		}

		array_push($this->where_lines, $line);
		$this->setInWhereColumns($line->getParamColumns());
		$this->setInParamObject($line->getColumn(), $line->getValue());
		$this->where_value_types .= $line->getType();

		return True;
	}

	public function addANull($column) {

		array_push($this->where_lines, new SelectWhereLine($column, 'IS NULL'));
		return True;
	}

	public function createANull($column, $conjunction='NULL') {
		return new SelectWhereLine($column, 'IS NULL', $conjunction);
	}

	public function insertANull($line) {

		if ($this->where_lines == NULL)  {
			$this->where_lines = array();
		}

		array_push($this->where_lines, $line);
	}

	public function appendANull($conjunction='AND', $column) {

		array_push($this->where_lines, new SelectWhereLine($column, 'IS NULL', $conjunction));
		return True;
	}

	public function addAParenthetical($lines, $conjunction) {

		if ($this->where_lines == NULL)  {
			$this->where_lines = array();
		}

		if ($this->where_value_types == NULL) {
			$this->where_value_types = '';
		}

		$parenthetical = new SelectWhereParenthetical($lines, $conjunction);
		array_push($this->where_lines, $parenthetical);

		$columns = $parenthetical->getColumns();
		$values_array = $parenthetical->getAssociativeValues();

		foreach ($columns as $column) {
			$this->setInParamObject($column, $values_array[$column]);
		}

		$this->setInWhereColumns($parenthetical->getParamColumns());
		$this->where_value_types .= $parenthetical->getTypeString();
	}

	public function getParamObject() {
		return $this->param_obj;
	}

	public function getWhereColumns() {
		return $this->where_columns;
	}

	/*
	 * Checks to make sure if the column count
	 * matches the amount of values stored in the array
	 * 
	 * If they don't match, we're likely to get an error from
	 * any db query
	 */
	public function columnCountMatchesValues() {

		$column_count = count($this->where_columns);
		$value_count = 0;
		$unique_columns = array_unique($this->where_columns);

		// Loop through unique column names
		// - some of the columns might point to arrays
		// ... so the census taking is easier if we can just grab the count
		// 
		foreach ($unique_columns as $column) {

			if (isset( $this->param_obj->$column )) {

				// increment by 1 or by array count
				if (is_array( $this->param_obj->$column )) {
					$value_count += count($this->param_obj->$column);
				}
				else {
					$value_count += 1;
				}
			}
		}

		return $column_count === $value_count;
	}

	public function searchableClassToColumn($class, $table) {

		if ($table == 'networks') {

			if ($class == 'dobj\City') {
				return array(
					'location' => 'id_city_cur',
					'origin' => 'id_city_origin'
				);
			}
			
			else if ($class == 'dobj\Region') {
				return array(
					'location' => 'id_region_cur',
					'origin' => 'id_region_origin'
				);
			}

			else if ($class == 'dobj\Country') {
				return array(
					'location' => 'id_country_cur',
					'origin' => 'id_country_origin'
				);
			}

			else if ($class == 'dobj\Language') {
				return array(
					'location' => NULL,
					'origin' => 'id_language_origin'
				);
			}

			else {
				throw new \Exception('CustomSelectQuery::searchableClassToColumn: A valid class was not provided');
			}
		}
	}

	/*
	 * Takes a searchable and returns a set of SelectWhereLines
	 *
	 * This seemed like it needed to be done, multiple queries
	 * require a lot of where lines when trying to
	 * identify a specific searchable
	 *
	 */
	public function createWhereLinesFromSearchable($searchable, $table, $type=NULL, $using_conjunction=NULL) {

		$lines = array();

		if ($table === 'networks') {

			if (!in_array($type, array('origin', 'location'))) {
				throw new \Exception('CustomSelectQuery->createWhereLinesFromSearchable: Type passed is not valid. Must be \'origin\' or \'location\'');
			}

			// Set string variables in case null where lines
			// need to be generated
			//
			$city_string = NULL;
			$region_string = NULL;

			if ($type == 'origin') {
				$city_string = 'id_city_' . $type;
				$region_string = 'id_region_' . $type; 
			}
			else if ($type == 'location') {
				$city_string = 'id_city_cur';
				$region_string = 'id_region_cur'; 
			}

			$class = get_class($searchable);
			$column_array = $this->searchableClassToColumn( $class, $table );

			// add id column
			array_push($lines, $this->createWhereLine($column_array[$type], '=', $using_conjunction, $searchable->id,'i'));

			// Depending on the scope of the searchables, we'll have to add some NULLs to the query
			if ( in_array($class, array('dobj\Region', 'dobj\Country') )) {
				array_push($lines, $this->createANull($city_string, 'AND'));
			}

			if ( $class == 'dobj\Country' ) {
				array_push($lines, $this->createANull($region_string, 'AND'));
			}
		}

		return $lines;
	}
}

?>
