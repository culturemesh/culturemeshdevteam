<?php
namespace dal;

class CustomQuery {

	private $query_template;
	private $mustache;

	public function __construct() {

		$this->mustache = new \misc\MustacheComponent();
	}

	public function setQuery($template, $query_vars) {

		$this->query_template = $template;
		$this->query_vars = $query_vars;
	}

	public function setQueryTemplate($template) {

		$this->query_template = $template;
	}

	public function setParamObject() {

	}

	public function prepareText() {

		try {
			$query_text = $this->mustache->render($this->query_template, $this->query_vars);
		}
		catch(\Exception $e) {

		}

		return $query_text;
	}
}
