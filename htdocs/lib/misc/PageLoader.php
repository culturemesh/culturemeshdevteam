<?php
namespace misc;

class PageLoader {

	private $cm;
	private $mobile_detect;

	public function __construct($cm, $mobile_detect) {

		if (get_class($cm) !== 'Environment') {
		  throw new \Exception('PageLoader::construct - A valid environment was not passed in');
		}

		// load cm
		$this->cm = $cm;
		$this->mobile_detect = $mobile_detect;
	}

	public function generate($user_template, $vars) {

		$base_template = 'base.html';

		if ($this->mobile_detect->isMobile())
		  $base_template = 'base.html';	// will change into mobile thing

		// base layout
		$base = file_get_contents($this->cm->template_dir . $this->cm->ds . $base_template);

		if (!$base) {
		  throw new \Exception('PageLoader::generate - Base file not found');
		}

		// get engine
		$m = new \Mustache_Engine(array(
		  'pragmas' => array(\Mustache_Engine::PRAGMA_BLOCKS),
		  'partials' => array(
		    'layout' => $base
		  ),
		));

		// get actual template
		$template = file_get_contents(\Environment::$site_root . $this->cm->ds . $user_template);

		if (!$template) {
		  throw new \Exception('PageLoader::generate - Template file: ' . $template . 'not found.');
		}

		return $m->render($template, $vars);
	}
}
