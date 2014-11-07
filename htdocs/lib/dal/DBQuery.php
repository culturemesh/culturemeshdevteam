<?php
namespace dal;

class DBQuery extends DBOp{

	/*
	 * Single query execution function
	 */
	public function execute($args) {
		
		// the connection
		$result = $this->connection->
				prepare($this->query);

		// check which class we're using
		$class = get_class($result);

		for ($i = 0;$i<count($args);$i++) {
			$result->bindParam($i+1, $args[$i]);
		}

		// bind params, if they exist
		/*
		if (count($args) > 0) {
			$ref = new \ReflectionClass($class);
			$method = $ref->getMethod("bind_param");
			$method->invokeArgs($result, $args);
		}
		 */

		// execute...beast spirit evolution
		$result->execute();
		return $result;

	}

	public function getName() {

		return $this->name;
	}
}
?>
