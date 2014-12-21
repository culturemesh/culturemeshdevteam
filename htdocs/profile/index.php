<?php
require('../Environment.php');
$cm = new Environment();

// set up the router
$al = new AltoRouter();
$al->setBasePath($cm->f_root . $cm->ds . 'profile');

// set up maps
$al->map('GET', '/', function() { echo 'No profile chosen'; }, 'nonet');
$al->map('GET', '/[i:id]', 'control\Profile#match', 'match'); 

$match = $al->match();

$call = misc\Util::getController($match['target']);

// call the controller and the matching action and send params
$call['controller']::$call['action']($cm, $match['params']);

?>
