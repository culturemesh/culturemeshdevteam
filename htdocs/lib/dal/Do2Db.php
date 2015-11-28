<?php
namespace dal;

class Do2Db {

	private $dal;
	private $dobj;
	private $query;
	private $op;

	public function execute($dal, $dobj, $query) {

		$op = $dal->getCQuery($query);

		$this->load($dal, $dobj, $query, $op);

		if ($op == NULL) {
			$this->cleanse();
			throw new \Exception('Operation hasn\'t been loaded. Have you forgotten to return it in the registration function? That happens a lot.');
		}

		$scheme = $op->getScheme();
		$args = $this->prepareArgs($scheme);
		$result = $op->execute($args, $scheme['param_types']);

		$final = $this->processResult($scheme, $result);
		$this->cleanse();

		return $final;
	}

	public function isEmpty() {

		// get own attributes
		$vars = get_object_vars($this);

		// check for an activated variable
		foreach ($vars as $var) {
			if ($var != NULL)
				return false;
		}

		return true;
	}

	/*
	 * Returns an array of arguments (from the dobj) 
	 * based on the scheme required from the dbquery
	 *
	 */
	private function prepareArgs($scheme) {

		$args = array();
		// deprecate for pdo
		//array_push($args, $scheme['param_types']);
		$params = $scheme['params'];

		// check for misplaced array
		if ( is_array($this->dobj) ) {
			$this->cleanse();
			throw \Exception('No array can be used to pass parameters, cast as an object.');
		}

		foreach ($params as $param) {
			if ($this->dobj->$param === NULL &&
				!in_array($param, $scheme['nullable'])) {
					throw new \InvalidArgumentException("{$param} is not a parameter in ". get_class($this->dobj));
					$this->cleanse();
			}
			else {
				$thing = &$this->dobj->getReference($param);

				if (is_array($thing)) {
				
					// Push everything from the array at once
					if ($scheme['params_stack'] === NULL) {
						foreach($thing as $item_in_thing) {
							array_push($args, $item_in_thing);
						}
					}
					else {
						// Only push into array when parameter calls for it
						$item_in_thing = array_shift($thing);
						array_push($args, $item_in_thing);
					}
				}
				else {
					array_push($args, $thing);
				}

			}
		}
		
		return $args;
	}

	private function processResult($scheme, $result) {

		// error handling for pure statements
		if ($scheme['returning'] == False) {
			
			if ($result->errorInfo()[0] == NULL) 
				return True;
			else
				return $result->errorInfo();
		}

		// if result set is empty
		if ($result->rowCount() == 0) {
			return $result;
		}

		// error handling for queries
		if ($scheme['returning_list'] == True)
			return $this->fillList($scheme, $result);
		else if ($scheme['returning_assoc'] == True) {
			return $this->fillAssoc($scheme, $row = $result->fetch(\PDO::FETCH_ASSOC));
		}
		else if ($scheme['returning_value'] == True) {
			$row = $result->fetch(\PDO::FETCH_ASSOC);
			$cols = $scheme['returning_cols'];

			// return single value
			return $row[$cols[0]];
		}
		else
			return $this->fillObj($scheme, $row = $result->fetch(\PDO::FETCH_ASSOC));
	}

	/**
	 * fill with row
	 */
	private function fillObj($scheme, $row) {

		// uses base dobj class to create dobj of any type
		$dobj = $scheme['returning_class']::createFromDataRow($row);
		return $dobj;
	}

	private function fillAssoc($scheme, $row) {

		$arr = array();

		// fill assoc array
		foreach ($scheme['returning_cols'] as $key) {
			$value = $row[$key];
			$arr[$key] = $value;
		}

		return $arr;
	}

	/**
	 * calls fillObj multiple times
	 */
	private function fillList($scheme, $result) {

		$list = new \dobj\DObjList();

		// loop through stuff
		foreach ($result->fetchAll(\PDO::FETCH_ASSOC) as $row) {
			$dobj = $this->fillObj($scheme, $row);
			$list->dInsert($dobj);
		}
		/*
		while ($row = $result->fetch_assoc()) {
			$dobj = $this->fillObj($scheme, $row);
			$list->dInsert($dobj);
		}
		 */

		return $list;
	}

	private function load($dal, $dobj, $query, $op) {
	
		// set things
		$this->dal = $dal;
		$this->dobj = $dobj;
		$this->query = $query;
		$this->op = $op;
	}

	private function cleanse() {

		$this->dal = NULL;
		$this->dobj = NULL;
		$this->query = NULL;
		$this->op = NULL;
	}

	public function initializeCustomQuery() {

		return new CustomSelectQuery();
	}
}

?>
