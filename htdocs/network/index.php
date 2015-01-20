<?php
// ----------------------------------------------------------------------------------------------------
// - Display Errors
// ----------------------------------------------------------------------------------------------------
ini_set('display_errors', 'On');
ini_set('html_errors', 0);

// ----------------------------------------------------------------------------------------------------
// - Error Reporting
// ----------------------------------------------------------------------------------------------------
error_reporting(-1);

// ----------------------------------------------------------------------------------------------------
// - Shutdown Handler
// ----------------------------------------------------------------------------------------------------
function ShutdownHandler()
{
    if(@is_array($error = @error_get_last()))
    {
        return(@call_user_func_array('ErrorHandler', $error));
    };

    return(TRUE);
};

register_shutdown_function('ShutdownHandler');

// ----------------------------------------------------------------------------------------------------
// - Error Handler
// ----------------------------------------------------------------------------------------------------
function ErrorHandler($type, $message, $file, $line)
{
    $_ERRORS = Array(
        0x0001 => 'E_ERROR',
        0x0002 => 'E_WARNING',
        0x0004 => 'E_PARSE',
        0x0008 => 'E_NOTICE',
        0x0010 => 'E_CORE_ERROR',
        0x0020 => 'E_CORE_WARNING',
        0x0040 => 'E_COMPILE_ERROR',
        0x0080 => 'E_COMPILE_WARNING',
        0x0100 => 'E_USER_ERROR',
        0x0200 => 'E_USER_WARNING',
        0x0400 => 'E_USER_NOTICE',
        0x0800 => 'E_STRICT',
        0x1000 => 'E_RECOVERABLE_ERROR',
        0x2000 => 'E_DEPRECATED',
        0x4000 => 'E_USER_DEPRECATED'
    );

    if(!@is_string($name = @array_search($type, @array_flip($_ERRORS))))
    {
        $name = 'E_UNKNOWN';
    };

    return(print(@sprintf("%s Error in file \xBB%s\xAB at line %d: %s\n", $name, @basename($file), $line, $message)));
};

$old_error_handler = set_error_handler("ErrorHandler");
require('../Environment.php');
$cm = new Environment();

// start session
session_name('myDiaspora');
session_start();

// set up the router
$al = new AltoRouter();

if (strpos($cm->f_root, 'live') == False)
  $al->setBasePath($cm->f_root . $cm->ds . 'network');
else
  $al->setBasePath('/network');

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
