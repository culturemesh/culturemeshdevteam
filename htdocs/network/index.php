<?php
require('../Environment.php');
$cm = new Environment();

// start session
session_name('myDiaspora');
session_start();

// set up the router
$al = new AltoRouter();
$al->setBasePath($cm->f_root . $cm->ds . 'network');

// set up maps
$al->map('GET', '/', function() { echo 'No network chosen'; }, 'nonet');
$al->map('GET', '/[i:id]/', 'control\Network#match', 'match_slash'); 
$al->map('GET', '/[i:id]', 'control\Network#match', 'match'); 
$al->map('GET', '/test', 'control\Network#test', 'test');

$match = $al->match();

$call = misc\Util::getController($match['target']);

// call the controller and the matching action and send params
$call['controller']::$call['action']($cm, $match['params']);

// index out

//call_user_func_array($match['target'], $match['params']);

/*
if ($match['name'] == 'match') {

	$id = $match['params']['id'];

	// prepare for db
	$dal = new dal\DAL($cm->getConnection());
	$dal->loadFiles();
	$do2db = new dal\Do2Db();

	// load network
	$network = dobj\Network::createFromId($id, $dal, $do2db);

	if ($network == False) {
		echo 'No network found';
	}
	
	else {
		var_dump($network);
	}

	// close connection
	$cm->closeConnection();
}
 */

/*
// base layout
$base = $cm->getBaseTemplate();

// get engine
$m = new Mustache_Engine(array(
  'pragmas' => array(Mustache_Engine::PRAGMA_BLOCKS),
  'partials' => array(
    'layout' => $base
  ),
));

if (isset($_SESSION['uid']))
	$logged_in = true;
else
	$logged_in = false;

// get actual site
$template = file_get_contents(__DIR__.$cm->ds.'templates'.$cm->ds.'index.html');
$page_vars = array(
	'vars' => $cm->getVars(),
	'logged_in' => $logged_in
);

echo $m->render($template, $page_vars);
 */
?>
