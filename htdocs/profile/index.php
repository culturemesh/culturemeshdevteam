<?php

require('../environment.php');
$cm = new Environment();

// set up the router
$al = new AltoRouter();

if (strpos($cm->f_root, 'live') == False)
  $al->setBasePath($cm->f_root . $cm->ds . 'profile');
else
  $al->setBasePath('/profile');

// success maps
$al->map('GET', '/[i:id]', 'control\Profile#match', 'match'); 
$al->map('GET', '/[i:id]/', 'control\Profile#match', 'match_slash'); 

// fail maps
$al->map('GET', '/', 'control\Profile#fail', 'no_arg');
$al->map('GET', '/[*]', 'control\Profile#fail', 'fail_alpha'); 
$al->map('GET', '/[*]/', 'control\Profile#fail', 'fail_alpha_slash'); 

$match = $al->match();

$call = misc\Util::getController($match['target']);

// call the controller and the matching action and send params
$call['controller']::$call['action']($cm, $match['params']);

?>
