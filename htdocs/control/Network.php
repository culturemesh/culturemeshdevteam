<?php
namespace control;

class Network {

	public static function test($cm, $params) {
		echo 'Test chamber';
	}

	/*
	 * Network Id has been input for search n stuff
	 *
	 */
	public static function match($cm, $params) {

		//echo $cm->host_root;
//		$stuffs = $cm->getVars();
//		echo $stuffs['home_path'];

		$nid = $params['id'];

		// set session var
		$_SESSION['cur_network'] = $nid;

		// prepare for db
		$dal = new \dal\DAL($cm->getConnection());
		$dal->loadFiles();
		$do2db = new \dal\Do2Db();

		// load network
		$network = \dobj\Network::createFromId($nid, $dal, $do2db);

		// 404 Redirect
		if ($network == False) {
			header('Location: ' . $cm->host_root . $cm->ds . '404.php');
		}

		$network->getPosts($dal, $do2db);
		$network->getEvents($dal, $do2db);
		$network->getPostCount($dal, $do2db);
		$network->getMemberCount($dal, $do2db);


		// check if user is logged in
		// check registration
		$site_user = NULL;
		$logged_in = false;

		if (isset($_SESSION['uid'])) {
			$logged_in = true;

			// check if user is registered
			// if so, get user info
			$site_user = \dobj\User::createFromId($_SESSION['uid'], $dal, $do2db);

			// see if user is registered
			// in network
			$member = $network->checkRegistration($site_user->id, $dal, $do2db);
			$guest = false;
		}

		// close connection
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


		/////// make components //////////
		$m_comp = new \misc\MustacheComponent();

		// set network stuff
		$network->posts->setMustache($m_comp);

		try
		{
			$p_html = $network->posts->getHTML('network', array(
				'cm' => $cm,
				'network' => $network,
				'site_user' => $site_user,
				'mustache' => $m_comp
				)
			);
		}
		catch (\Exception $e)
		{
			$p_html = NULL;
		}

		$network->events->setMustache($m_comp);

		if ($network->events_sect) {
		  $network->events_sect->setMustache($m_comp);

		try 
		{
			$tmp = file_get_contents($cm->template_dir . $cm->ds . 'network-event-cardtable.html');
			$ec_html = $network->events_sect->getHTML('card', array(
				'cm' => $cm,
				'network' => $network,
				'mustache' => $m_comp,
				'list_template' => $tmp
				)
			);
		}
		catch (\Exception $e)
		{
			$ec_html = NULL;
		}
		}

		try
		{
			$em_html = $network->events->getHTML('modal', array(
				'cm' => $cm,
				'network' => $network,
				'site_user' => $site_user,
				'mustache' => $m_comp
				)
			);
		}
		catch (\Exception $e)
		{
			$em_html = NULL;
		}

		// check if we need more posts
		$more_posts = false;
		if ($network->post_count > 10) {
			$more_posts = true;
		}

		$map_embed_template = file_get_contents($cm->template_dir . $cm->ds . 'gmap-embed.html');
		$map_embed = $m_comp->render($map_embed_template, array(
			'key' => $cm->g_api_key,
			'location' => $network->location->toString()));

		$searchbar_template = file_get_contents($cm->template_dir . $cm->ds . 'searchbar.html');
		$searchbar = $m_comp->render($searchbar_template, array('vars' => $cm->getVars()));

		// get actual site
		$template = file_get_contents(\Environment::$site_root . $cm->ds . 'network' . $cm->ds . 'templates'.$cm->ds.'index.html');
		$page_vars = array(
			'sections' => array(
				'map_embed' => $map_embed,
				'searchbar' => $searchbar,
				'lrg_network' => 'Large Network',
				'network_title' => $network->getTitle(),
				'posts' => $p_html,
				'event_slider' => $ec_html,
				'event_modals' => $em_html),
			'vars' => $cm->getVars(),
			'test' => "<b>Something</b>",
			'page_vars' => array (
				'member' => $member,
				'member_count' => $network->member_count,
				'more_posts' => $more_posts,
				'post_count' => $network->post_count,
				'uid' => null,
				'nid' => $nid,
				'get' => $_GET
				),
			'logged_in' => $logged_in,
			'site_user' => $site_user
		);

		echo $m->render($template, $page_vars);
	}
}

?>