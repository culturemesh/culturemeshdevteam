<?php
namespace control;

class Search {

	public static function fail($cm, $params) {

		// 404 redirect
		$er = new \nav\ErrorRedirect($cm, '404');
		$er->execute();
	}

	public static function match($cm, $params) {

		$cm->displayErrors();

		// start session
		session_name($cm->session_name);
		session_start();

		$search_array = array(
			'search_one' => $_GET['search-1'],
			'search_two' => $_GET['search-2'],
			'verb' => $_GET['verb'],
			'click_1' => $_GET['clik1'],
			'click_2' => $_GET['clik2'],
			'var_id' => $_GET['varId'],
			'var_class' => $_GET['varClass'],
			'loc_id' => $_GET['locId'],
			'loc_class' => $_GET['locClass'],
			'origin_searchable' => NULL,
			'location_searchable' => NULL
		);

		$search_type = 'searchable';

		$origin_searchable = NULL;
		$location_searchable = NULL;
		$main_network = NULL;

		if ($_GET['clik1'] == 1 && $_GET['clik2'] == 1) {

			$search_type = 'network';

			// Set up searchables from data
			$origin_searchable = new $search_array['var_class'];
			$origin_searchable->id = (int) $search_array['var_id'];
			$origin_searchable->name = $search_array['search-1'];

			$location_searchable = new $search_array['loc_class'];
			$location_searchable->id = (int) $search_array['loc_id'];
			$location_searchable->name = $search_array['search-2'];

			$main_network = new \dobj\Network();
			$main_network->origin_searchable = $origin_searchable;
			$main_network->location_searchable = $location_searchable;

			$search_array['origin_searchable'] = $origin_searchable;
			$search_array['location_searchable'] = $location_searchable;
		}


		/*
		$search_one = $_GET['search-1'];
		$search_two = $_GET['search-2'];
		$verb = $_GET['verb'];
		$click_1 = $_GET['clik1'];
		$click_2 = $_GET['clik2'];
		 */

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
		$network_search = new \search\FullNetworkSearch($search_array, $search_type);

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
		if ($search_type == 'searchable') {
			for($i = 0; $i < count($results); $i++) {

				$html = NULL;
				$ARRAY_LENGTH = 7;

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
						$slice = $results[$i]->slice( 0, $ARRAY_LENGTH, true );
						$slice->setMustache($m_comp);
						$template = file_get_contents($cm->template_dir . $cm->ds . 'user-results_searchable_options.html');
						$html = $slice->getHTML('user-results', array(
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
		}
		else {

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
