<?php
namespace api;

class TwitterQuery {

	private	$search_base = 'https://api.twitter.com/1.1/search/tweets.json';
	private $query = '?q=';
	private $request_method = 'GET';
	private $language_assignment = 'lang:';

	private $op_space = '%20';
	private $op_hashtag = '%23';
	private $op_or = 'OR';

	/*
	 * No overloaded constructors...
	 *
	 * But we're still going to allow the user to build a query
	 * with one line of code
	 */
	public function __construct($network=NULL) {

		// Checking to make sure we're getting a network
		//
		if ($network != NULL) {

			if (get_class($network) != 'dobj\Network') 
				throw new \Exception('TwitterQuery: Cannot build a query, was not passed a dobj\Network');

			$this->buildSearch($network);
		}

	}

	/*
	 * Gets what is required for a network, produces a 
	 * twitter query from it
	 */
	public function buildSearch($network) {

		if (get_class($network) != 'dobj\Network') 
			throw new \Exception('TwitterQuery: Cannot build a query, was not passed a dobj\Network');

		// get current location
		$location = $network->getLowestLocationComponent();
		$hashtag_location = $this->makeHashtag($location);

		// get origin
		$origin = $network->getLowestOriginComponent();
		$hashtag_origin = $this->makeHashtag($origin);

		// add common terms
		$this->addQueryTerm($location);
		$this->addQueryTerm($hashtag_location);

		// special language considerations
		if ($network->network_class == '_l') {
			
			$language_code = $this->getLanguageCode($origin);

			// add special terms
			$this->addLanguageTerm($language_code);
		}
		else {

			$country = $network->getHighestOriginComponent();

			// check to see if country has a language component
			// dothis();

			// add special terms
			$this->addQueryTerm($origin);
			$this->addQueryTerm($hashtag_origin);
		}

	}

	/*
	 * Checks to see if string needs to be modified to make
	 * a hashtag, 
	 */
	private function makeHashtag($string) {

		if (strpos($string, ' ') === False)
			return NULL;

		return $this->op_hashtag . $this->removeSpace($string);
	}

	/*
	 * Removes spaces from a string
	 *   needed for hashtag searches
	 */
	private function removeSpace($string) {
		return str_replace(' ', '', $string);
	}

	private function replaceSpace($string) {
		return str_replace(' ', $this->op_space, $string);
	}

	/*
	 * Takes a string with slash separated values, and returns an
	 * array filled with the values
	 *
	 * Useful for grabbing 
	 */
	private function explodeSlash($string) {
		return explode('/', $string);
	}

	/*
	 * Adds a term to the query,
	 *   usually gives a li'l space between new things
	 */
	private function addQueryTerm($term_arg) {

		if ($term_arg != False && $term_arg != NULL) {

			// check to see if this is the first query argument
			// if it isn't, adds or and a space
			//
			$end = substr($this->query, -3);

			if ($end !== '?q=') {
				$this->query .= $this->op_space . $this->op_or . $this->op_space;
			}

			// add the terms
			// if they are packaged in an array,
			// do stuff like this
			//
			if (is_array($term_arg)) {

				foreach ($term_arg as $term) {
					$this->query .=  $this->op_space . $this->op_or . $this->op_space . $this->replaceSpace( $term_arg );
				}
			}
			else {
				$this->query .= $this->replaceSpace( $term_arg );
			}
		}
	}

	private function addLanguageTerm($term_arg) {

		if ($end !== '?q=') {
			$this->query .= $this->op_space . $this->op_or . $this->op_space;
		}

		$this->query .= $this->language_assignment . $term_arg;
	}

	/*
	 * Check to see if a country has a specific language supported
	 */
	private function checkCountryForLanguage() {

	}


	/*
	 * Returns the query string API call that is to be made
	 *
	 **** For testing purposes ****

	 */
	public function getBaseUrl() {
		return $this->search_base;
	}

	/*
	 * Returns the query string API call that is to be made
	 *
	 **** For testing purposes ****

	 */
	public function getQuery() {
		return $this->query;
	}

	/*
	 * Returns the actual API call that is to be made
	 *
	 **** For testing purposes ****

	 */
	public function getSearch() {
		return $this->search_base . $this->query;
	}

	public function getRequestMethod() {
		return $this->request_method;
	}	

	/*
	 * Search twitter json for language codes
	 *
	 * Returns False if no language found, that way,
	 * the query adder will know that nothing should
	 * be added 
	 */
	private function getLanguageCode($target_lingo) {

		$languages = file_get_contents('lib/api/twitter-languages.json');
		$languages_json = json_decode($languages, true);

		foreach($languages_json as $language) {

			if (strpos($language['name'], $target_lingo) !== False)
			       return $language['code'];	
		}

		return false;
	}
}

?>
