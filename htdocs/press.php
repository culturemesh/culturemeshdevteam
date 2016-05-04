<?php
/*
    include_once 'zz341/fxn.php';
    include 'environment.php';
    $cm = new Environment();
    $cm->displayErrors();
    $ppre = "press_pg";
//    include 'page_tpl.php';
    include 'reg_page_tpl.php';
 */

	include("environment.php");
	$cm = new Environment();

	session_name($cm->session_name);
	session_start();

	$cm->enableDatabase($dal, $do2db);

	$logged_in = false;
	$site_user = NULL;

	// USER STUFF
	if (isset($_SESSION['uid'])) {

		$logged_in = true;

		// check if user is registered
		// if so, get user info
		$site_user = \dobj\User::createFromId($_SESSION['uid'], $dal, $do2db)->prepare($cm);
	}

	// get press
	$press = $do2db->execute($dal, NULL, 'getPress');

	$cm->closeConnection();

	$m_comp = new \misc\MustacheComponent();
	$press_html = $press->getHTML('press', array(
		'cm' => $cm,
		'mustache' => $m_comp));

	$page_loader = new \misc\PageLoader($cm);
	echo $page_loader->generate('templates' . $cm->ds .'press.html', array(
		'vars' => $cm->getVars(),
		'logged_in' => $logged_in,
		'site_user' => $site_user,
		'press' => $press_html
	));

	/*
	
    <?php
    //may need these ids for css purposes..
    //id="careers" class="info-pg"
    //TODO:MAKE QUERY FOR MEDIA, THEN LOOP
    $press_posts = getRowsQuery("SELECT * FROM internal_press ORDER BY date DESC");
    foreach($press_posts as $post):
        $thumb = ($post['thumb_url'] != NULL) ? "images/" . $post['thumb_url'] : "images/CM_Logo_Final_square.jpg";//need default img
    ?>
    <div class="media">
        <a class="pull-left" href="#">
          <img class="media-object" data-src="<?php echo $thumb; ?>" alt="64x64" style="width: 258px; height: 166px;" src="<?php echo $thumb;?>">
        </a>
        <div class="media-body">
	  <h4 class="media-heading"><a target="_blank" href="<?php echo $post['url'] ?>"><?php echo $post['title'];?></a></h4>
          <h5 class="media-heading press_subtitle"><?php echo $post['sub_title'];?></h5>
          <div class="press_body">
          <?php echo $post['body'];?>
          </div>
        </div>
      </div>
    <?php endforeach;?>

	*/
?>
