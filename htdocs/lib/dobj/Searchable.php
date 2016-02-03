<?php
namespace dobj;

class Searchable extends DisplayDObj {

	protected $name;
	protected $tweet_terms;
	protected $tweet_terms_override;

	public function toString() {
		return $name;
	}

	public function display($context) {

	}

	/*
	 * Returns an array with all the nice object properties
	 */
	public function prepare() {

		return array(
			'name' => $this->name,
			'fullname' => $this->toString(),
			'id' => $this->id,
			'class' => get_class($this)
		);
	}

	public function getHTML($context, $vars) {

		$cm = $vars['cm'];
		$mustache = $vars['mustache'];

		switch($context) {

		case 'user-results':

			$radio_name = $vars['radio_name'];

			// get template
			$template = file_get_contents($cm->template_dir . $cm->ds . 'user-results_searchable.html');
			return $mustache->render($template, array(
				'searchable' => $this->prepare(),
				'radio_name' => $radio_name
				)
			);
		}
	}
	
	/*
	 * Worry about context later
	 */
	public function getJSON($context=NULL) {

		return array(
			'name' => $this->name,
			'fullname' => $this->toString(),
			'id' => $this->id,
			'obj_class' => get_class($this),
			'type' => 'xx'
		);
	}
}

?>
