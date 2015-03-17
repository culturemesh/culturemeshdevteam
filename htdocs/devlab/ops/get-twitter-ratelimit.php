<?php
include("../../environment.php");
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

$url = 'https://api.twitter.com/1.1/application/rate_limit_status.json';
$getfield = '?resources=search';
$requestMethod = 'GET';

$twitter = new TwitterAPIExchange($settings);
$dump = $twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest();

echo $dump;
?>
