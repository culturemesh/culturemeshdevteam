<?php
namespace api;

class Twitter {

	/*
	 * Takes a json response from Twitter's Search API
	 * and turns it into a dobjList of dobjTweets
	 *
	 */
	public static function JsonToTweets($json_input, $remora=NULL) {

		$list = new \dobj\DObjList();

		foreach ($json_input['statuses'] as $json_tweet) {

			$tweet = new \dobj\Tweet();
			$tweet->fillFromJson($json_tweet, $remora);

			$list->dInsert( $tweet );
		}

		return $list;
	}

	/*
	 * Takes a string representing some language as input
	 *   returns a two-byte language code
	 *
	 *   For use in twitter's GET search
	 */
	public static function GetLanguageCodeFromLanguage($input_language) {

		$langs = file_get_contents('twitter-languages.json');
		$langs_json = json_decode($langs, TRUE);

		// rudimentary search
		  // AND I DO MEAN RUDE!!
		foreach($langs_json as $lang) {

			if ($lang['name'] == $input_language) {

				return $lang['code'];
			}
		}

		// if we're this far, we've found nothing
		return False;
	}
}
?>
