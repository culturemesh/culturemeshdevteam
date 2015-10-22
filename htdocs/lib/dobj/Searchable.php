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

	public function getHTML($context, $vars) {

		$cm = $vars['cm'];
		$mustache = $vars['mustache'];

		switch($context) {

		case 'user-results':

			// get template
			$template = file_get_contents($cm->template_dir . $cm->ds . 'user-results_searchable.html');
			return $mustache->render($template, array(
				'name' => $this->toString(),
				)
			);
		}
	}
}

?>
