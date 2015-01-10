<?php
namespace misc;

class MustacheComponent {

	protected $m;

	public function __construct() {

		$this->m = new \Mustache_Engine;
	}

	public function render($template, $vars) {
		return $this->m->render($template, $vars);
	}
}
