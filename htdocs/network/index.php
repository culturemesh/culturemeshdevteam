<?php
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

//////////////// IMPORTANT ////////////////////////////////////
// call the controller and the matching action and send params
//
    $call['controller']::$call['action']($cm, $match['params']);
//
//////////////////////////////////////////////////////////
?>
