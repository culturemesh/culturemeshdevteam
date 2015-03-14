<?php

include("../../environment.php");
$cm = new \Environment();

require_once("vendor/j7mbo/twitter-api-php/TwitterAPIExchange.php");

// twitter api call
$settings = array(
    'oauth_access_token' => $GLOBALS['TWITTER_OAUTH_ACCESS_TOKEN'],
    'oauth_access_token_secret' => $GLOBALS['TWITTER_OAUTH_ACCESS_SECRET'],
    'consumer_key' => $GLOBALS['TWITTER_CONSUMER_KEY'],
    'consumer_secret' => $GLOBALS['TWITTER_CONSUMER_SECRET']
);

$url = 'https://api.twitter.com/1.1/help/languages.json';
$requestMethod = 'GET';

$twitter = new TwitterAPIExchange($settings);
$dump = $twitter->buildOauth($url, $requestMethod)
             ->performRequest();

echo json_encode($dump);
