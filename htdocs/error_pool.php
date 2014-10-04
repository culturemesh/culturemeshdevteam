<?php

/*
 * For tracking errors of 
 * multiple important operations
 *
 * Provide a stack
 */

class ErrorPool {

	private $stack;

	// default constructor
	function __construct() {

		$this->stack = array();
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
}

class CMError {

	public $result;	// bool
	public $msg;
	public $op;

	function __construct($result, $msg, $operation) {

		$this->result = $result;
		$this->msg = $msg;
		$this->op = $op;
	}
}

?>
