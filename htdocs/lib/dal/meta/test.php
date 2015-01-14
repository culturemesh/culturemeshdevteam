<?php
namespace dal\meta;

class test extends \dal\DBOp {

	protected $name = 'test';
	protected $query = 'test';

	protected $params = array('test');
	protected $returning = True;
	protected $returning_list = False;
	protected $returning_class = 'test';


	public function execute($args, $testing) {

		// 
		// nothing
	}

	public function getName() {
		return $this->name;
	}
}

?>
