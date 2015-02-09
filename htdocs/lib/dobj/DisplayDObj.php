<?php
namespace dobj;

abstract class DisplayDObj extends DObj {

	protected $m; // Mustache Engine
	protected $templates = array();
	protected $cur_template;

	abstract public function display($context);
	abstract public function getHTML($context, $vars);

	/*
	 * Necessary for mustache
	 */
	public function __isset($name) {
		return isset($this->$name);
	}

	/*
	 * Prepares dobj for display with mustache
	 * @params - Environment object
	 */
	public function prepare($cm) {

	}

	protected function startMustache($layout) {

		// get engine
		$this->m = new Mustache_Engine(array(
		  'pragmas' => array(Mustache_Engine::PRAGMA_BLOCKS),
		  'partials' => array(
		    'layout' => $layout
		  ),
		));
	}

	protected function loadTemplate($ltemp) {

		$this->cur_template = file_get_contents($templates[$ltemp]);
	}
}
