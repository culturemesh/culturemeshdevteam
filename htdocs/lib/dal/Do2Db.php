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

		$scheme = $op->getScheme();
		$args = $this->prepareArgs($scheme);
		$result = $op->execute($args);

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

	private function prepareArgs($scheme) {

		$args = array();
		// deprecate for pdo
		//array_push($args, $scheme['param_types']);
		$params = $scheme['params'];

		foreach ($params as $param) {
			try {
				if ($this->dobj->$param == NULL) {
					throw new \InvalidArgumentException("{$param} is not a parameter in ". get_class($this->dobj));
				}

				$thing = &$this->dobj->getReference($param);
				array_push($args, $thing);
			}
			catch (Exception $e) {

			}
		}
		
		return $args;
	}

	private function processResult($scheme, $result) {

		if ($scheme['returning'] == False)
			return $result;

		if ($scheme['returning_list'] == True)
			return $this->fillList($scheme, $result);
		else
			return $this->fillObj($scheme, $row = $result->fetch(\PDO::FETCH_ASSOC));
	}

	/**
	 * fill with row
	 */
	private function fillObj($scheme, $row) {

		$dobj = $scheme['returning_class']::createFromDataRow($row);
		return $dobj;
	}

	/**
	 * calls fillObj multiple times
	 */
	private function fillList($scheme, $result) {

		$list = new dobj\DObjList();

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
}

?>
