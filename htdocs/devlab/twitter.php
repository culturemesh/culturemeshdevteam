<?php
include("../environment.php");
$cm = new \Environment();

require_once("vendor/j7mbo/twitter-api-php/TwitterAPIExchange.php");

// base layout
$base = file_get_contents($cm->template_dir . $cm->ds . 'base.html');

// twitter api call
$settings = array(
    'oauth_access_token' => $GLOBALS['TWITTER_OAUTH_ACCESS_TOKEN'],
    'oauth_access_token_secret' => $GLOBALS['TWITTER_OAUTH_ACCESS_SECRET'],
    'consumer_key' => $GLOBALS['TWITTER_CONSUMER_KEY'],
    'consumer_secret' => $GLOBALS['TWITTER_CONSUMER_SECRET']
);

$url = 'https://api.twitter.com/1.1/search/tweets.json';
$getfield = '?q=%40twitterapi';
$requestMethod = 'GET';

$twitter = new TwitterAPIExchange($settings);
$dump = $twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest();

$tweets = json_decode($dump, true);

$list = api\Twitter::JsonToTweets($tweets);

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
$template = file_get_contents(__DIR__.$cm->ds.'templates'.$cm->ds.'twitter.html');
$page_vars = array(
	'vars' => $cm->getVars(),
	'logged_in' => $logged_in,
	'tweet' => $list[0]->getInfo()
);

echo $m->render($template, $page_vars);
?>
