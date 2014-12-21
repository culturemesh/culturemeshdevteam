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

		$user = \dobj\User::createFromId($uid, $dal, $do2db);
		var_dump($user);

		// check registration
		$guest = true;
		if ($uid == $_SESSION['uid'])
			$guest = false;

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

		// get actual site
		$template = file_get_contents(\Environment::$site_root . $cm->ds . 'profile' . $cm->ds . 'templates'.$cm->ds.'index.html');
		$page_vars = array(
			'user' => $user,
			'sections' => array(
				'searchbar' => $searchbar),
			'vars' => $cm->getVars(),
			'test' => "<b>Something</b>",
			'page_vars' => array (
				'member_count' => $network->member_count,
				'post_count' => $network->post_count,
				'guest' => $guest,
				'uid' => $user->id,
				'nid' => $nid
				),
			'logged_in' => $logged_in
		);

		echo $m->render($template, $page_vars);
	}
}
