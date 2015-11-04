<?php
namespace search;

class NullSearchResult {

	protected $m; // Mustache Engine
	protected $templates = array();
	protected $cur_template;

	// display variables
	protected $input;
	protected $alternate;

	public function setUserInput($input) {
		$this->input = $input;
	}

	public function setAlternate($alternate) {
		$this->alternate = $alternate;
	}

	public function setMessage() {

	}

	public function display($context) {

	}

	public function getHTML($context, $vars) {

		$cm = $vars['cm'];
		$mustache = $vars['mustache'];

		switch($context) {

		case 'user-results':

			// get template
			$template = file_get_contents($cm->template_dir . $cm->ds . 'user-results_searchable-not-found.html');
			return $mustache->render($template, array(
				'name' => $this->input,
				'alternate' => $this->alternate
				)
			);
		}
	}

	/*
	 * Necessary for mustache
	 */
	public function __isset($name) {
		return isset($this->$name);
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

?>
