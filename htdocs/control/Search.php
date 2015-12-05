<?php
namespace control;

class Search {

	public static function fail($cm, $params) {

		// 404 redirect
		$er = new \nav\ErrorRedirect($cm, '404');
		$er->execute();
	}

	public static function match($cm, $params) {

		// start session
		session_name($cm->session_name);
		session_start();

		$search_one = $_GET['search-1'];
		$search_two = $_GET['search-2'];

		$metaphone_1 = \misc\Util::DoubleMetaphone($search_one);
		$metaphone_2 = \misc\Util::DoubleMetaphone($search_two);

		// prepare for db
		$dal = new \dal\DAL($cm->getConnection());
		$dal->loadFiles();
		$do2db = new \dal\Do2Db();

		if (isset($_SESSION['uid'])) {

			$logged_in = true;

			// check if user is registered
			// if so, get user info
			$site_user = \dobj\User::createFromId($_SESSION['uid'], $dal, $do2db)->prepare($cm);
		}

		// RUN SEARCH
		$network_search = new \search\FullNetworkSearch(array(
			'search-1' => $search_one,
			'search-2' => $search_two));

		$search_manager = new \search\SearchManager($cm, $dal, $do2db, $network_search);
		$results = $search_manager->getResults();

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

		/*
		// map embed
		$map_embed_template = file_get_contents($cm->template_dir . $cm->ds . 'gmap-embed.html');
		$map_location = $network->location->toString();

		// fixes an issue that made the state GA display and not the country
		if ($map_location == 'Georgia')
			$map_location = 'Country Georgia';

		$map_embed = $m_comp->render($map_embed_template, array(
			'key' => $cm->g_api_key,
			'location' => $map_location));
		 */

		// searchbar
		$searchbar_template = file_get_contents($cm->template_dir . $cm->ds . 'searchbar.html');
		$searchbar = $m_comp->render($searchbar_template, array('vars' => $cm->getVars()));
		$origin_results = NULL;
		$location_results = NULL;

		// RESULTS LISTS
		for($i = 0; $i < count($results); $i++) {

			$html = NULL;

			if ($i == 0)
			  $radio_name = 'origin';

			if ($i == 1)
			  $radio_name = 'location';

			try {
				// Check if result is a Null result
				if (get_class($results[$i]) == 'search\NullSearchResult') {

					$html = $results[$i]->getHTML('user-results', array(
						'cm' => $cm,
						'mustache' => $m_comp
						)
					);
				}
				else {
					$results[$i]->setMustache($m_comp);
					$template = file_get_contents($cm->template_dir . $cm->ds . 'user-results_searchable_options.html');
					$html = $results[$i]->getHTML('user-results', array(
						'list_template' => $template,
						'cm' => $cm,
						'radio_name' => $radio_name,
						'mustache' => $m_comp
						)
					);
				}
			}
			catch (\Exception $e)
			{
				$html = "<ul id='results' class='network'></ul>";
			}

			if ($i == 0)
			  $origin_results = $html;

			if ($i == 1)
			  $location_results = $html;
		}

		// load templates
		//
		$possible_network_template = file_get_contents($cm->template_dir . $cm->ds . 'user-results_possible-network.html');
		$active_network_template = file_get_contents($cm->template_dir . $cm->ds . 'user-results_active-network.html');

		// get actual site
		$template = file_get_contents(\Environment::$site_root . $cm->ds . 'search' . $cm->ds . 'templates'.$cm->ds.'index.html');
		$page_vars = array(
			'sections' => array(
				'map_embed' => $map_embed,
				'searchbar' => $searchbar,
				'origin-results' => $origin_results,
				'location-results' => $location_results),
			'templates' => array(
				'possible_network' => $possible_network_template,
				'active_network' => $active_network_template),
			'vars' => $cm->getVars(),
			'page_vars' => array (
				'uid' => null,
				'nid' => $nid,
				'get' => $_GET
			),
			'logged_in' => $logged_in,
			'site_user' => $site_user
		);

		// display the page proudly, chieftain
		echo $m->render($template, $page_vars);
	}
}

?>
