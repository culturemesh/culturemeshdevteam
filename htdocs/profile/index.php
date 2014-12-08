<?php
require('../Environment.php');
$cm = new Environment();

// set up the router
$al = new AltoRouter();
$al->setBasePath($cm->f_root . $cm->ds . 'profile');

// set up maps
$al->map('GET', '/', function() { echo 'No profile chosen'; }, 'nonet');
$al->map('GET', '/[i:id]', function($id) { echo 'Profile: '.$id; }, 'network'); 

$match = $al->match();
call_user_func_array($match['target'], $match['params']);

?>
