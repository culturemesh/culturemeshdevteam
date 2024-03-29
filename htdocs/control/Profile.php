<?php
namespace control;

class Profile {

	public static function fail($cm, $params) {
		// 404 redirect
		$er = new \nav\ErrorRedirect($cm, '404');
		$er->execute();
	}

	public static function match($cm, $params) {

		$mobile_detect = new \misc\MobileDetect();

		// start session
		session_name($cm->session_name);
		session_start();

		$uid = $params['id'];

		// prepare for db
		$dal = new \dal\DAL($cm->getConnection());
		$dal->loadFiles();
		$do2db = new \dal\Do2Db();

		// get profile user
		$user = \dobj\User::createFromId($uid, $dal, $do2db);

		if ($user == False) {
			// 404 redirect
			$er = new \nav\ErrorRedirect($cm, '404');
			$er->execute();
		}

		// get user information
		// -- events
		$user->getEventsInYourNetworks($dal, $do2db);
		$user->getEventsHosting($dal, $do2db);
		$user->getEventsAttending($dal, $do2db);

		// -- posts
		$user->getPosts($dal, $do2db);

		// -- networks
		//$user->getNetworksWithPosts($dal, $do2db);
		//$user->getNetworksWithEvents($dal, $do2db);
		$user->getMemberNetworks($dal, $do2db);

		if (get_class($user->yh_events) == 'PDOStatement') {
			$err = $user->yh_events->errorInfo();
		}

		// check registration
		$guest = true;
		$site_user = NULL;

		if (isset($_SESSION['uid'])) {
			$logged_in = true;
			$site_user = \dobj\User::createFromId($_SESSION['uid'], $dal, $do2db)->prepare($cm);

			if ($site_user->id == $user->id) {
				$guest = false;
			}
		}

		// END 
		$cm->closeConnection();

		$page_loader = new \misc\PageLoader($cm, $mobile_detect);
		/*
		// base layout
		$base = $cm->getBaseTemplate();

		// get engine
		$m = new \Mustache_Engine(array(
			'pragmas' => array(\Mustache_Engine::PRAGMA_BLOCKS),
			'partials' => array(
				'layout' => $base
			),
		));
		 */

		// mustache components
		$m_comp = new \misc\MustacheComponent();

		$searchbar_template = file_get_contents($cm->template_dir . $cm->ds . 'searchbar.html');
		$sb_standard = $m_comp->render($searchbar_template, array('network' => True, 'vars' => $cm->getVars()
								));

		$sb_alt_font = $m_comp->render($searchbar_template, array('alt-font' => True, 'alt-color' => True, 'network'=> True,
									'vars' => $cm->getVars()
								));
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

		if ($user->yn_events) {
			$tmp = file_get_contents($cm->template_dir . $cm->ds . 'dashboard-eventul.html');
			$yn_event_html = $user->yn_events->getHTML('dashboard', array(
				'cm' => $cm,
				'mustache' => $m_comp,
				'list_template' => $tmp,
				'site_user' => $site_user
				)
			);
		}

		if ($user->yh_events) {
			$tmp = file_get_contents($cm->template_dir . $cm->ds . 'dashboard-eventul.html');
			$yh_event_html = $user->yh_events->getHTML('dashboard', array(
				'cm' => $cm,
				'mustache' => $m_comp,
				'list_template' => $tmp,
				'site_user' => $site_user
				)
			);
		}

		if ($user->ya_events) {
			$tmp = file_get_contents($cm->template_dir . $cm->ds . 'dashboard-eventul.html');
			$ya_event_html = $user->ya_events->getHTML('dashboard', array(
				'cm' => $cm,
				'mustache' => $m_comp,
				'list_template' => $tmp,
				'site_user' => $site_user
				)
			);
		}

		$more_posts = false;
		if ($user->yp_posts) {
			$c = $user->yp_posts->countAll();
			if ($user->yp_posts->countAll() > 10) {
				$more_posts = true;
			}

			$tmp = file_get_contents($cm->template_dir . $cm->ds . 'dashboard-postul.html');
			$yp_post_html = $user->yp_posts->getHTML('dashboard', array(
				'cm' => $cm,
				'mustache' => $m_comp,
				'list_template' => $tmp,
				'max' => 10
				)
			);
		}

		// get actual site
		$template = file_get_contents(\Environment::$site_root . $cm->ds . 'profile' . $cm->ds . 'templates'.$cm->ds.'index.html');
		$page_vars = array(
			'user' => $user->prepare($cm),
			'site_user' => $site_user,
			'searchbars' => array(
				'standard' => $sb_standard,
				'alt-font' => $sb_alt_font
			),
			'sections' => array(
				'yn_networks' => $yn_net_html,
				'yn_events' => $yn_event_html,
				'ya_events' => $ya_event_html,
				'yh_events' => $yh_event_html,
				'yp_posts' => $yp_post_html),
			'vars' => $cm->getVars(),
			'logged_in' => $logged_in,
			'test' => "<b>Something</b>",
			'page_vars' => array (
				'guest' => $guest,
				'uid' => $user->id,
				'more_posts' => $more_posts
				),
		);

		echo $page_loader->generate( 'profile' . $cm->ds . 'templates'.$cm->ds.'index.html',
			$page_vars);

		//echo $m->render($template, $page_vars);
	}
}
