<?php
namespace control;
class Network {


	public static function fail($cm, $params) {
		// 404 redirect
		$er = new \nav\ErrorRedirect($cm, '404');
		$er->execute();
	}

	/*
	 * Network Id has been input for search n stuff
	 *
	 */
	public static function match($cm, $params) {

		$nid = $params['id'];

		$mobile_detect = new \misc\MobileDetect();

		// set session var
		$_SESSION['cur_network'] = $nid;

		// new cache object
		$cache = new \misc\Cache($cm);

		// prepare for db
		$dal = new \dal\DAL($cm->getConnection());
		$dal->loadFiles();
		$do2db = new \dal\Do2Db();
		// load network
		$network = \dobj\Network::createFromId($nid, $dal, $do2db);

		if ($network == False) {
			// 404 Redirect
			$er = new \nav\ErrorRedirect($cm, '404');
			$er->execute();
		}

		if (isset($_GET['plink'])) {
			$network->getOlderPostsFromId($dal, $do2db, (int) $_GET['plink']);
		}
		else {
			$network->getPosts($dal, $do2db);
		}

		$network->getTweets($dal, $do2db);

		$network->getEvents($dal, $do2db);
		$network->getPostCount($dal, $do2db);
		$network->getMemberCount($dal, $do2db);

		$page_tweet_count = 0;

		if (!$mobile_detect->isMobile()) {
			$tweet_manager = new \api\TweetManager($cm, $network, $dal, $do2db);
			$tweets = $tweet_manager->requestTweets();
			$query_info = $tweet_manager->getQueryInfo();

			//add tweets to posts
			$network->mergePostsAndTweets( $tweets );

			$page_tweet_count = count($tweets);
		}

		// add tweets to post count
		$page_post_count = $network->native_post_count;
		$network->post_count += $page_tweet_count;

		// put tweets and posts all together
		//
		// RANK
		// 1) posts ( < 30 days old)
		// 2) tweets w replies ( < 30 days old)
		// 3) tweets
		// 4) posts ( >= 30 days old)
		// 5) tweets w replies ( >= 30 days old)
		//
		$network->posts = $network->posts->splits(function( $obj ) {

			if ( get_class($obj) == 'dobj\Tweet') {

				// GET THE DATE
				$now = new \DateTime();
				$date = new \DateTime( $obj->created_at );

				// returns diffence between dates,
				// forced to be positive
				$interval = $now->diff($date, true);
				$diff = (int) $interval->format('%a');

				// a tweet with native posts
				if (count($obj->replies) > 0) {

					
					if ($diff < 30) {

						return array (
							'section' => 2,
							'key' => 'rank');
					}
					else {

						return array (
							'section' => 5,
							'key' => 'rank');
					}
				}
				else {

					return array (
						'section' => 3,
						'key' => 'rank');
				}
			} // end if

			else if (get_class($obj) == 'dobj\Post') {

				$now = new \DateTime();
				$date = new \DateTime( $obj->post_date );

				// returns diffence between dates,
				// forced to be positive
				$interval = $now->diff($date, true);
				$diff = (int) $interval->format('%a');

				if ($diff < 30) {
					return array (
						'section' => 1,
						'key' => 'rank');
				} // endif 

				else {
					return array (
						'section' => 4,
						'key' => 'rank');
				} // end else

			} // end else if

		}, 'inline'); // end function

		$network->posts->order('rank', 'asc');

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

		// close connection
		$cm->closeConnection();

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

		$page_loader = new \misc\PageLoader($cm, $mobile_detect);

		/////// make components //////////
		$m_comp = new \misc\MustacheComponent();

		// set network stuff
		$network->posts->setMustache($m_comp);

		try
		{
			$template = file_get_contents($cm->template_dir . $cm->ds . 'network-postwall.html');
			$p_html = $network->posts->getHTML('network', array(
				'list_template' => $template,
				'cm' => $cm,
				'network' => $network,
				'site_user' => $site_user,
				'mustache' => $m_comp
				)
			);
		}
		catch (\Exception $e)
		{
			$p_html = "<ul id='post-wall-ul' class='network'></ul>";
		}

		$network->events->setMustache($m_comp);

		if ($network->events_sect) {

			$network->events_sect->setMustache($m_comp);

			try
			{
				$tmp = file_get_contents($cm->template_dir . $cm->ds . 'network-event-divul.html');
				$ec_html = $network->events_sect->getHTML('div', array(
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

		/*
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
		 */

		// check if we need more posts
		$more_post_content = True;
		$more_posts = False;
		$more_tweets = True; // make default for now
		$until_date = new \DateTime($query_info['until_date']);
		$until_date = $until_date->format('Y-m-d');
		$cur_query_roster = 0;	// 0 for false

		if ($page_post_count > 10) {

			if ($page_post_count > 10)
				$more_posts = True;

			$older_posts_lower_bound = 10;
			$newer_posts_lower_bound = NULL;
		}

		// if cur_query_roster is set to 0, upon the start of search
		// for more tweets, it will search for both components
		//
		// however, if the tweet count ain't high enough at the start,
		// there's no point
		//
		// don't start tweet search by looking for both components
		//
		if ($page_tweet_count < 15) {
			
			$cur_query_roster = 1;
			$until_date = 0;
		}
		else {

			// MUST ALSO CHECK DATE
			//
			// To make sure there will be tweets to be found with this query
			//
			$earliest_tweet_date = new \DateTime($query_info['until_date']);
			$now = new \DateTime();

			// now check to see if we've past the 10 days mark
			$new_difference = $now->diff($earliest_tweet_date);
			$query_exhausted = (int) $new_difference->format('%a') >= 8 || count($tweets) < 15;

			if ($query_exhausted) {
				$cur_query_roster = 1;
				$until_date = 0;
			}
		}

		// GET MAP (desktop only)

		$map_embed = NULL;

		if (!$mobile_detect->isMobile()) {

			$map_embed_template = file_get_contents($cm->template_dir . $cm->ds . 'gmap-embed.html');
			$map_location = $network->location->toString();

			// fixes an issue that made the state GA display and not the country
			if ($map_location == 'Georgia')
				$map_location = 'Country Georgia';

			if ($map_location == "New York, United States") {
				$map_location = 'State of New York';
			}

			$map_embed = $m_comp->render($map_embed_template, array(
				'key' => $cm->g_api_key,
				'location' => $map_location));
		}

		// searchbar
		$searchbar_template = file_get_contents($cm->template_dir . $cm->ds . 'searchbar.html');
		$sb_standard = $m_comp->render($searchbar_template, array('network' => True,
									'vars' => $cm->getVars()
								));

		$sb_alt_font = $m_comp->render($searchbar_template, array('alt-font' => True, 'alt-color' => True, 'network' => True,
									'vars' => $cm->getVars()
								));

		// social network buttons
		$sharebutton_template = file_get_contents($cm->template_dir . $cm->ds . 'sharebutton.html');
		$sharebuttons= $m_comp->render($sharebutton_template, array(
			'vars' => $cm->getVars(),
			'network' => $network));

		// load component templates
		//
		$event_overlay_template = file_get_contents($cm->template_dir . $cm->ds . 'network-event-overlay.html');
		$post_template = file_get_contents($cm->template_dir . $cm->ds . 'network-post.html');
		$tweet_template = file_get_contents($cm->template_dir . $cm->ds . 'network-tweet.html');
		$reply_template = file_get_contents($cm->template_dir . $cm->ds . 'network-reply.html');

		// get actual site
		$template = file_get_contents(\Environment::$site_root . $cm->ds . 'network' . $cm->ds . 'templates'.$cm->ds.'index.html');
		$page_vars = array(
			'searchbars' => array(
				'standard' => $sb_standard,
				'alt-font' => $sb_alt_font
			),
			'sections' => array(
		//		'sharebuttons' => $sharebuttons,
				'map_embed' => $map_embed,
				'lrg_network' => 'Large Network',
				'network_title' => $network->getTitle(),
				'post_wall' => $p_html,
				'event_slider' => $ec_html,
				'event_modals' => $em_html),
			'templates' => array(
				'event_overlay' => $event_overlay_template,
				'post' => $post_template,
				'tweet' => $tweet_template,
				'reply' => $reply_template
			),
			'vars' => $cm->getVars(),
			'test' => "<b>Something</b>",
			'page_vars' => array (
				'member' => $member,
				'member_count' => $network->member_count,
				'more_post_content' => $more_post_content,
				'more_posts' => $more_posts,
				'more_tweets' => 1,
				'cur_query_roster' => $cur_query_roster,
				'last_updated' => 0,
				'newer_posts' => $network->more_newer_posts,
				'newer_posts_lower_bound' => $newer_posts_lower_bound,
				'older_posts_lower_bound' => $older_posts_lower_bound,
				'post_count' => $network->post_count,
				'tweet_until_date' => $until_date,
				'tweet_scope_info' => $network->getScopeInfo(),
				'uid' => null,
				'nid' => $nid,
				'get' => $_GET
			),
			'logged_in' => $logged_in,
			'site_user' => $site_user
		);

		echo $page_loader->generate('network' . $cm->ds . 'templates'. $cm->ds .'index.html',
			$page_vars);

		/*
		// display the page proudly, chieftain
		echo $m->render($template, $page_vars);
		 */
	}
}
?>
