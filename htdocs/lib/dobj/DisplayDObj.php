<?php
namespace dobj;

abstract class DisplayDObj extends DObj {

	protected $m; // Mustache Engine
	protected $templates = array();
	protected $cur_template;

	abstract public function display($context);
	abstract public function getHTML($context);

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
