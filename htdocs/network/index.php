<?php

require('../environment.php');
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

// success maps
$al->map('GET', '/[i:id]/', 'control\Network#match', 'match_slash'); 
$al->map('GET', '/[i:id]', 'control\Network#match', 'match'); 

// fail maps
$al->map('GET', '/[*]', 'control\Network#fail', 'fail_alpha'); 
$al->map('GET', '/[*]/', 'control\Network#fail', 'fail_alpha_slash'); 
$al->map('GET', '/', 'control\Network#fail', 'no_arg');

$match = $al->match();

$call = misc\Util::getController($match['target']);

//////////////// IMPORTANT ////////////////////////////////////
// call the controller and the matching action and send params
//
    $call['controller']::$call['action']($cm, $match['params']);
//
//////////////////////////////////////////////////////////
?>
