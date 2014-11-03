<?php

/*
 * For tracking errors of 
 * multiple important operations
 *
 * Provide a stack
 */

class ErrorPool {

	private $stack;
	private $length;

	// default constructor
	function __construct() {

		$this->stack = array();
		$length = 0;
	}

	public function checkError($response) {

		while ($error = array_pop($this->stack)) {

			// stop if 
			if ($error->result == false) {

				$result['error'] = 1;
				$result['error_msg'] = $error->msg;
				echo json_encode($response);
				exit();
			}
		}
		
		// if nothing is wrong
		$result['error'] = 0;
		echo json_encode($response);
		exit();
	}

	public function addError($error) {

		// push error
		array_push($this->stack, $error);

		// add to length
		$this->length += 1;
	}

	public function checkStop($response) {

		if( end($this->stack)->result == false) {

			// set msg`
			$response['error'] = 1;
			$response['error_msg'] = end($this->stack)->msg;	

			// leave
			echo json_encode($response);
			exit('exited');
		}
		else {

			// reset stack pointer
			reset($this->stack);
		}
	}

	public function checkLength() {
		
		// return length
		return $this->length;
	}
}

class CMError {

	public $result;	// bool
	public $msg;
	public $op;

	function __construct($result, $msg, $operation) {

		// a little firm emphasis on stuff
		if (!is_bool($result)) {
			throw new Exception('Expected boolean as first parameter');
		}

		if (!is_string($msg)) {
			throw new Exception('Expected string as second parameter');
		}

		if (!is_string($operation)) {
			throw new Exception('Expected string as third parameter');
		}

		$this->result = $result;
		$this->msg = $msg;
		$this->op = $op;
	}
}

?>
