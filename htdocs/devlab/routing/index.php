<?php
require "../../Environment.php";
$cm = new Environment();

// now i can autoload this thing
$al = new AltoRouter();
//echo Environment::$site_root . $cm->ds . 'devlab' . $cm->ds . 'routing' . $cm->ds;
//$al->setBasePath(Environment::$site_root . $cm->ds . 'devlab' . $cm->ds . 'routing');
//echo 'culturemesh/culturemeshdevteam/htdocs/devlab/routing';
//$al->setBasePath('/devlab/routing');
$al->setBasePath('/culturemesh/culturemeshdevteam/htdocs/devlab/routing');
$al->map('GET|POST','/', function() { echo 'Basic'; }, 'home');
$al->map('GET|POST','/network/[i:id]', function($id) { echo 'Network: '.$id; }, 'network');
$al->map('GET','/profile/[i:id]', function($id) { echo 'User: '. $id; }, 'user');

$match = $al->match();
var_dump($match['params']);
/*
var_dump($match['target']);
var_dump($match['params']);
var_dump($match['name']);
 */

call_user_func_array($match['target'], $match['params']);
?>
