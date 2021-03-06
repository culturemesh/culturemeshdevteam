<?php
namespace misc;

class PageLoader {

	private $cm;
	private $mobile_detect;

	public function __construct($cm, $mobile_detect) {

		if (get_class($cm) !== 'Environment') {
		  throw new \Exception('PageLoader::construct - A valid environment was not passed in');
		}
		/*
		// check if user is logged in
		// check registration
		$site_user = NULL;
		$logged_in = false;
		$member = false;

		if (isset($_SESSION['uid'])) {

			$logged_in = true;

			// check if user is registered
			// if so, get user info
			$site_user = \dobj\User::createFromId($_SESSION['uid'], $dal, $do2db)->prepare($cm);

			// see if user is registered
			// in network
			$member = $site_user->checkNetworkRegistration($nid);//$network->checkRegistration($site_user->id, $dal, $do2db);
			$guest = false;
		}
		 */

		// load cm
		$this->cm = $cm;
		$this->mobile_detect = $mobile_detect;
	}

	public function generate($user_template, $vars) {

		$base_template = 'base.html';

		if (get_class($this->mobile_detect) !== "misc\MobileDetect") {
			$base_template = 'base.html';
		}
		else {
			if ($this->mobile_detect->isMobile())
			  $base_template = 'base.html';	// will change into mobile thing
		}

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
