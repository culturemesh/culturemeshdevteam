<?php
namespace dobj;

class Press extends DisplayDObj {

	protected $title;
	protected $url;
	protected $sub_title;
	protected $body;
	protected $thumb_url;
	protected $date;
	protected $last_updated;

	public function display($context) {

	}

	public function getHTML($context, $vars) {

		if (!is_string($context)) {
		  throw new \Exception('Press::getHTML - Proper context not provided. Use \'press\'');
		}

		// get vars
		$cm = $vars['cm'];
		$mustache = $vars['mustache'];

		switch($context) {

		case 'press':

			// get template
			$template = file_get_contents($cm->template_dir . $cm->ds . 'press-article.html');

			if ($this->thumb_url == NULL) {
			  $this->thumb_url = 'CM_Logo_Final_square.jpg';
			}

			return $mustache->render($template, array(
				'press' => $this));
		}
		
	}
}

?>
