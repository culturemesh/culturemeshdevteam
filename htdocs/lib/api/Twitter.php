<?php
namespace api;

class Twitter {

	public static function JsonToTweets($json_input) {

		$list = new \dobj\DObjList();

		foreach ($json_input['statuses'] as $json_tweet) {

			$tweet = new \dobj\Tweet();
			$tweet->fillFromJson($json_tweet);

			$list->dInsert( $tweet );
		}

		return $list;
	}
}
?>
