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

$path = '/home3/culturp7/public_html/culturemesh-live/htdocs' . '/lib/api/twitter-languages.json';

if (file_put_contents($path, $dump ) ) {

	echo json_encode(array('error' => 0));
	exit();
}

else {
	echo json_encode(array('error' => 1,
				'path' => $path));
	exit();
}

?>
