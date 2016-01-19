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

		$search_array = array(
			'search_one' => $_GET['search-1'],
			'search_two' => $_GET['search-2'],
			'verb' => $_GET['verb'],
			'click_1' => $_GET['clik1'],
			'click_2' => $_GET['clik2'],
			'var_id' => (int) $_GET['varId'],
			'var_class' => $_GET['varClass'],
			'loc_id' => (int) $_GET['locId'],
			'loc_class' => $_GET['locClass'],
			'origin_searchable' => NULL,
			'location_searchable' => NULL
		);

		$search_type = 'searchable';

		$origin_searchable = NULL;
		$location_searchable = NULL;
		$main_network = NULL;

		// prepare for db
		$cm->enableDatabase($dal, $do2db);

		if (isset($_SESSION['uid'])) {

			$logged_in = true;

			// check if user is registered
			// if so, get user info
			$site_user = \dobj\User::createFromId($_SESSION['uid'], $dal, $do2db)->prepare($cm);
		}

		// initialize search manager
		$search_manager = new \search\SearchManager($cm, $dal, $do2db, NULL);

		if ($_GET['clik1'] == 1 && $_GET['clik2'] == 1) {

			$search_type = 'network';

			// Here's a little time saving technique,
			// lump the searches in with one another
			if ($search_array['var_class'] == $search_array['loc_class']) {

				$ids = array($search_array['var_id'], $search_array['loc_id']);
				$combined_search = new \search\SearchableGroupIdSearch( $ids, $search_array['var_class']);

				$search_manager->setSearch($combined_search);
				$results = $search_manager->getResults();

				if ($results == False) {

					// Set up searchables from data
					$origin_searchable = new $search_array['var_class'];
					$origin_searchable->id = (int) $search_array['var_id'];
					$origin_searchable->name = $search_array['search_one'];

					$location_searchable = new $search_array['loc_class'];
					$location_searchable->id = (int) $search_array['loc_id'];
					$location_searchable->name = $search_array['search_two'];
				}
				else {
					foreach($results as $r) {
						if ($r->id == $search_array['var_id'])
						  $origin_searchable = $r;
						else
						  $location_searchable = $r;
					}
				}
			}
			else {
				$origin_search = new \search\SearchableIdSearch($search_array['var_id'], $search_array['var_class']);
				$location_search = new \search\SearchableIdSearch($search_array['loc_id'], $search_array['loc_class']);

				$search_manager->setSearch( $origin_search );
				$origin_searchable = $search_manager->getResults();

				$search_manager->setSearch( $location_search );
				$location_searchable = $search_manager->getResults();
			}

			$main_network = new \dobj\Network();
			$main_network->origin_searchable = $origin_searchable;
			$main_network->location_searchable = $location_searchable;

			$search_array['origin_searchable'] = $origin_searchable;
			$search_array['location_searchable'] = $location_searchable;
		}

		// RUN SEARCH
		$network_search = new \search\FullNetworkSearch($search_array, $search_type);
		$search_manager->setSearch($network_search);

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

		// Results components 
		$origin_results = array(
			'hidden' => True,
			'origins' => NULL);

		$location_results = array(
			'hidden' => True,
			'locations' => NULL);

		$network_results = array(
			'hidden' => True,
			'network' => NULL);

		$related_results = array(
			'hidden' => True,
			'networks' => NULL);

		// RESULTS LISTS
		if ($search_type == 'searchable') {

			$origin_results['hidden'] = False;
			$location_results['hidden'] = False;

			$ARRAY_LENGTH = 7;

			// PROCESS ORIGIN RESULTS
			$slice = $results['origin']->slice( 0, $ARRAY_LENGTH, true );
			$slice->setMustache($m_comp);
			$template = file_get_contents($cm->template_dir . $cm->ds . 'user-results_searchable_options.html');
			$origin_html = $slice->getHTML('user-results', array(
				'list_template' => $template,
				'cm' => $cm,
				'radio_name' => 'origin',
				'mustache' => $m_comp
				));

			$origin_results['origins'] = $origin_html;

			// PROCESS LOCATION RESULTS
			$slice = $results['location']->slice( 0, $ARRAY_LENGTH, true );
			$slice->setMustache($m_comp);
			$template = file_get_contents($cm->template_dir . $cm->ds . 'user-results_searchable_options.html');
			$location_html = $slice->getHTML('user-results', array(
				'list_template' => $template,
				'cm' => $cm,
				'radio_name' => 'location',
				'mustache' => $m_comp
				));

			$location_results['locations'] = $location_html;

			/*
			for($i = 0; $i < count($results); $i++) {

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


				if ($i == 1)
				  $location_results = $html;
			}
			 */
		}

		// load templates
		//
		$possible_network_template = file_get_contents($cm->template_dir . $cm->ds . 'user-results_possible-network.html');
		$active_network_template = file_get_contents($cm->template_dir . $cm->ds . 'user-results_active-network.html');

		if ($search_type == 'network') {

			// decide if main network is active or possible
			// -- might need to do it up THERE
			//

			if ($results['main_network'] !== False)
			  $main_network = $results['main_network'];

			$network_results['hidden'] = false;

			$network_results['network'] = $main_network->getHTML('search', array(
					'cm' => $cm,
					'mustache' => $m_comp
				));


			// handle related networks
			// // double array so that template can be simplified
			$related_results['hidden'] = false;
			$related_results['networks'] = array();

			foreach ($results['related_networks'] as $related_network) {

				// render
				$rn_html = $related_network->getHTML('search', array(
					'cm' => $cm,
					'mustache' => $m_comp
				));

				// add to array
				array_push($related_results['networks'], $rn_html);
			}
		}


		// get actual site
		$template = file_get_contents(\Environment::$site_root . $cm->ds . 'search' . $cm->ds . 'templates'.$cm->ds.'index.html');
		$page_vars = array(
			'sections' => array(
				'map_embed' => $map_embed,
				'searchbar' => $searchbar,
				'origin_results' => $origin_results,
				'location_results' => $location_results,
				'network_results' => $network_results,
				'related_results' => $related_results),
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
