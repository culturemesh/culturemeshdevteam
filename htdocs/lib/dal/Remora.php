<?php
namespace dal;

class Remora {

	private $registry;
	private $EXECUTION_COUNT = 0;

	public function __construct() {
		$this->registry = array();
	}

	public function __get($param) {
		return $this->registry[$param];
	}

	public function __set($member, $arg) {
		$this->registry[$member] = $arg;
	}

	/*
	 * Sets the function to be executed by the Remora
	 *
	 * If the thing wants to have multiple callbacks,
	 * we'll handle that too.
	 *
	 * @param - $function : should be a Closure
	 */
	public function setFunction($function) {

		$fn = $function->bindTo($this);


		if (!isset($this->registry['function'])) {
			$this->registry['function'] = array($fn);
		} 

		else {
			array_push($this->registry['function'], $fn);
		}
	}

	/*
	 * Executes the function or functions that 
	 * are in the registry
	 *
	 * @param - $args : any arguments (usually the dobj)
	 *
	 */
	public function execute($args=NULL) {

		if (!isset($this->registry['function']))
			throw new \Exception('Remora: No function has been set to execute');

		foreach($this->registry['function'] as $fn) {
			$fn($args);
		}

		// count for debugging purposes
		$this->EXECUTION_COUNT += 1;

	}
}
