<?php
namespace nav;

class ErrorRedirect extends HTTPRedirect {

	protected $html_error;

	function __construct($cm, $html_error) {

		$this->cm = $cm;
		$this->html_error = $html_error;
		$this->error_page = $this->html_error . '.php';
	}

	function execute() {
		header('Location: //'. $this->cm->hostname. $this->cm->ds . $this->error_page);
	}
}
