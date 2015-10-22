<?php

require('../environment.php');
$cm = new Environment();

// set up the router
$al = new AltoRouter();

if (strpos($cm->f_root, 'live') == False)
  $al->setBasePath($cm->f_root . $cm->ds . 'search');
else
  $al->setBasePath('/search');

// success maps
$al->map('GET', '/', 'control\Search#match', 'match'); 
$al->map('GET', '/[*]', 'control\Search#match', 'match_alpha'); 
$al->map('GET', '/[*]/', 'control\Search#match', 'match_alpha_slash'); 

// fail maps
/*
$al->map('GET', '/', 'control\Search#fail', 'no_arg');
$al->map('GET', '/[*]', 'control\Search#fail', 'fail_alpha'); 
$al->map('GET', '/[*]/', 'control\Search#fail', 'fail_alpha_slash'); 
 */

$match = $al->match();

$call = misc\Util::getController($match['target']);

// call the controller and the matching action and send params
$call['controller']::$call['action']($cm, $match['params']);

?>
