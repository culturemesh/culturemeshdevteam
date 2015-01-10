<?php
namespace control;

class Profile {

	public static function match($cm, $params) {

		// start session
		session_name("myDiaspora");
		session_start();

		$uid = $params['id'];

		// prepare for db
		$dal = new \dal\DAL($cm->getConnection());
		$dal->loadFiles();
		$do2db = new \dal\Do2Db();

		// get profile user
		$user = \dobj\User::createFromId($uid, $dal, $do2db);

		// get user information
		// -- events
		$user->getEventsInYourNetworks($dal, $do2db);
		$user->getEventsHosting($dal, $do2db);
		$user->getEventsAttending($dal, $do2db);

		// -- posts
		$user->getPosts($dal, $do2db);

		// -- networks
		$user->getNetworksWithPosts($dal, $do2db);
		$user->getNetworksWithEvents($dal, $do2db);
		$user->getMemberNetworks($dal, $do2db);

		if (get_class($user->yh_events) == 'PDOStatement') {
			$err = $user->yh_events->errorInfo();
			print_r($err);
		}

		// check registration
		$guest = true;

		if (isset($_SESSION['uid']) && $uid == $_SESSION['uid']) {
			$guest = false;
		}

		// END 
		$cm->closeConnection();

		// base layout
		$base = $cm->getBaseTemplate();

		// get engine
		$m = new \Mustache_Engine(array(
			'pragmas' => array(\Mustache_Engine::PRAGMA_BLOCKS),
			'partials' => array(
				'layout' => $base
			),
		));

		// mustache components
		$m_comp = new \misc\MustacheComponent();

		$searchbar_template = file_get_contents($cm->template_dir . $cm->ds . 'searchbar.html');
		$searchbar = $m_comp->render($searchbar_template, array());

		$yn_net_html = NULL;
		$yn_event_html = NULL;
		$yh_event_html = NULL;
		$ya_event_html = NULL;
		$yp_post_html = NULL;

		if ($user->yn_networks)
		$yn_net_html = $user->yn_networks->getHTML('dashboard', array(
			'cm' => $cm,
			'mustache' => $m_comp
			)
		);

		if ($user->yn_events)
		$yn_event_html = $user->yn_events->getHTML('dashboard', array(
			'cm' => $cm,
			'mustache' => $m_comp
			)
		);

		if ($user->yh_events)
		$yh_event_html = $user->yh_events->getHTML('dashboard', array(
			'cm' => $cm,
			'mustache' => $m_comp
			)
		);

		if ($user->ya_events)
		$ya_event_html = $user->ya_events->getHTML('dashboard', array(
			'cm' => $cm,
			'mustache' => $m_comp
			)
		);

		if ($user->yp_posts)
		$yp_post_html = $user->yp_posts->getHTML('dashboard', array(
			'cm' => $cm,
			'mustache' => $m_comp
			)
		);

		// get actual site
		$template = file_get_contents(\Environment::$site_root . $cm->ds . 'profile' . $cm->ds . 'templates'.$cm->ds.'index.html');
		$page_vars = array(
			'user' => $user,
			'sections' => array(
				'searchbar' => $searchbar,
				'yn_networks' => $yn_net_html,
				'yn_events' => $yn_event_html,
				'ya_events' => $ya_event_html,
				'yh_events' => $yh_event_html,
				'yp_posts' => $yp_post_html),
			'vars' => $cm->getVars(),
			'test' => "<b>Something</b>",
			'page_vars' => array (
				'guest' => $guest,
				'uid' => $user->id,
				),
		);

		echo $m->render($template, $page_vars);
	}
}
