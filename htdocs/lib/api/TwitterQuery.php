<?php
namespace api;

class TwitterQuery {

	private	$search_base = 'https://api.twitter.com/1.1/search/tweets.json';


	/*
	 * Representation of the beginning of the query string.
	 * This will always be the first, so it must have a '?'
	 */
	private $query = '?q=';
	private $request_method = 'GET';
	private $language_assignment = '&lang=';

	/*
	 * Result assignment will always have an & because
	 * the query operand will always be the first
	 * element of any api call
	 */
	private $result_assignment = '&result_type=';

	private $op_space = '%20';
	private $op_quote = '%22';
	private $op_hashtag = '%23';
	private $op_or = 'OR';

	/*
	 * Table for converting languages
	 */
	private $language_table = array(
		'Mandarin Chinese' => 'zh-tw'
	);

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
	 *
	 * Need for each:
	 * 0) No spaces, no parentheses, no slashes
	 *	- direct input
	 *
	 * 1) Spaces
	 * 	- hashtag
	 * 	- quoted version
	 *
	 * 2) Parentheses
	 * 	- hashtag (what's in parentheses)
	 * 	- quoted version
	 *
	 * 3) Slashes
	 * 	- divide, check for 1 & 2
	 *
	 * Languages
	 * 	- must search for language code
	 * 	- since you may be receiving multiple values,
	 * 	add support for arrays
	 *
	 */
	public function buildSearch($network) {

		if (get_class($network) != 'dobj\Network') 
			throw new \Exception('TwitterQuery: Cannot build a query, was not passed a dobj\Network');

		// get current location
		$raw_location = $network->getLowestLocationComponent();
		$location_arg = $this->prepareComponent($raw_location);

		// get origin
		$raw_origin = $network->getLowestOriginComponent();
		$origin_component = $this->explodeSlash($raw_origin);
		$origin_arg = $this->prepareComponent($origin_component);

		$this->addArg($location_arg);

		// special language considerations
		if ($network->network_class == '_l') {
			
			// check for the language code
			$language_code = $this->getLanguageCode($origin_arg);

			if ($language_code === False || is_array($origin_arg)) {
				$this->addArg($origin_arg);
			}

			// add special terms
			$this->addLanguageTerm($language_code);
		}
		else {
			$this->addArg($origin_arg);
			/*

			// check to see if country has a language component
			//$country = $network->getHighestOriginComponent();
			// dothis();
			 */
		}

		// add result type
		$this->addResultType('mixed');
	}

	/*
	 * Creates a component array that can be easily parsed
	 * when it comes time to build the query
	 *
	 * Params - 
	 *	$component_arg - can be a string or an array
	 *
	 * Returns
	 * 	associative array OR array of associative arrays
	 *
	 * 	Array Structure:
	 * 		array(basic => the string with quotes, 
	 * 			hashtag => the string put into hashtag form)
	 */
	private function prepareComponent($component_arg) {


		if (is_array($component_arg))  {

			$return_component = array();

			foreach($component_arg as $component) {
				array_push($return_component, $this->prepareComponent($component));
			}
		}
		else {

			$return_component = array (
				'raw' => NULL,
				'basic' => NULL,
				'hashtag' => NULL
			);

			// check for parentheses
			$return_component['raw'] = $component_arg;
			$return_component['basic'] = $this->addQuotes($component_arg);

			// check if hashable
			$hashable = $this->determineStringHashability($component_arg);

			if ($hashable) {
				$return_component['hashtag'] = NULL;
			}
			else {
				$return_component['hashtag'] = $this->makeHashtag($component_arg);
			}
		}

		return $return_component;
	}

	/*
	 * Params:
	 * 	associative array OR array of associative arrays
	 *
	 * 	Array Structure:
	 * 		array(basic => the string with quotes, 
	 * 			hashtag => the string put into hashtag form)
	 */
	private function addArg($arg) {

		if (isset($arg[0])) {

			foreach ($arg as $a) {
				$this->addArg($a);
			}
		}
		else {

			// A null hashtag signifies an unnecessary hashtag,
			// the basic is enough
			//
			if ($arg['hashtag'] !== NULL ) {
				$this->addHashtag($arg['hashtag']);
			}

			// check to see if this is the first query argument
			// if it isn't, adds or and a space
			//
			$end = substr($this->query, -3);

			if ($end !== '?q=') {
				$this->query .= $this->op_space . $this->op_or . $this->op_space;
			}

			$this->query .= $this->replaceSpace( $arg['basic'] );
		}
	}

	/*
	 * Removes parentheses and spaces
	 *
	 * Params:
	 * 	$term_arg : single string
	 */
	private function makeHashtag($term_arg) {

		$clay = NULL;

		// get value within parentheses
		if (strpos('(', $term_arg) !== False) {
			preg_match('#\((.*?)\)#', $term_arg, $clay);
		}
		else {
			$clay = $term_arg;
		}

		if (strpos($clay, ' ') === False) {
			return $this->op_hashtag . $clay;
		}
		
		return $this->op_hashtag . $this->removeSpace($clay);
	}

	/*
	 * Adds a hashtag only if 
	 */
	private function addHashtag($term_arg) {

		// check to see if this is the first query argument
		// if it isn't, adds or and a space
		//
		$end = substr($this->query, -3);

		if ($end !== '?q=') {
			$this->query .= $this->op_space . $this->op_or . $this->op_space;
		}

		// replace spaces and add quotes
		$this->query .=  $term_arg;
	}

	/*
	 * Determine if a string needs to be modified in order
	 * to be made into a valid hashtag
	 *
	 * ie : Check if it has slashes or parentheses
	 *
	 * Params:
	 * 	$string - the string to be checked
	 */
	private function determineStringHashability($string) {

		if (strpos($string, '(') === False &&
			strpos($string, ' ') === False)
			return True;
		else
			return False;
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
	 * Adds quotes (url_encoded) around a search term in case the term
	 * has whitespace. This is necessary to control the "order of operations"
	 * in Twitter's Search API. Quotes can act something like parentheses.
	 *
	 * Params:
	 *   string - the string to be modified. Must be a string
	 *
	 * Returns the string, modified or not
	 */
	private function addQuotes($string) {

		if ( preg_match('/\s/', $string) ) 
			return $this->op_quote . $string . $this->op_quote;
		else
			return $string;
	}

	/*
	 * Calls addQuotes, and then replaceSpace
	 *   should be applicable to all query terms
	 *
	 *   Params:
	 *     $string - the special term to get all dolled up.
	 */
	private function prepareTerm($string) {

		return $this->replaceSpace( $this->addQuotes($string) );
	}

	/*
	 * Takes a string with slash separated values, and returns an
	 * array filled with the values
	 *
	 * Useful for grabbing 
	 */
	private function explodeSlash($string) {

		$test_array = explode('/', $string);

		if (count($test_array) > 1)
			return $test_array;
		else
			return $test_array[0];
	}

	/*
	 * Adds a term to the query,
	 *   usually gives a li'l space between new things
	 */
	private function addQueryTerm($term_arg) {

		// Do nothing if term_arg is empty
		//
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
					$this->query .=  $this->op_space . $this->op_or . $this->op_space . $this->prepareTerm( $term_arg );
				}
			}
			else {
				// replace spaces and add quotes
				$this->query .= $this->prepareTerm( $term_arg );
			}
		}
	}

	/*
	 * Adds a two letter language code argument to the query
	 *
	 * Params - 
	 *    $term_arg - The two letter language code
	 */
	private function addLanguageTerm($term_arg) {

		// If there is no term arg
		// make the language default to English
		//
		if ($term_arg === False) {
			$term_arg = 'en';
		}

		/*
		if ($end !== '?q=') {
			$this->query .= $this->op_space . $this->op_or . $this->op_space;
		}
		 */

		$this->query .= $this->language_assignment . $term_arg;
	}

	/*
	 * Adds result type parameter
	 *
	 * @params - 
	 * 	$term_arg : The result type to be added
	 * 	  can be mixed, recent, or popular
	 */
	private function addResultType($term_arg) {

		$valid_queries = array('mixed', 'recent', 'popular');

		if (!in_array($term_arg, $valid_queries))
			throw new \Exception('TwitterQuery: Added an invalid result type');

		$this->query .= $this->result_assignment . $term_arg;
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
	 * Params:
	 * 	$target_lingo : Must be an assoc array of this structure:
	 * 		array('raw' => x, 'basic' => y, 'hashtag' => z)
	 *
	 * 		Or an array of these buggers
	 *
	 * Returns False if no language found, that way,
	 * the query adder will know that nothing should
	 * be added 
	 */
	private function getLanguageCode($target_lingo) {

		// else, check out the json file
		$languages = file_get_contents('lib/api/twitter-languages.json');
		$languages_json = json_decode($languages, true);

		if (is_array($target_lingo)) {

			$result_array = array();

			foreach ($target_lingo as $lingo) {

				$result = $this->getLanguageCode($lingo['raw']);

				if ($result !== False)
					array_push($result_array, $result);
			}

			// Twitter only takes one language at a time,
			// so return the first correct value
			// ...until we have something better
			// 
			if (count($result_array) == 0)
				return false;
			else
				return $result_array[0];
		}
		else {

			// check the language array
			$lang_array_value = $this->language_table[$target_lingo];

			if ($lang_array_value !== NULL)
				return $lang_array_value;

			foreach($languages_json as $language) {

				if (strpos($language['name'], $target_lingo) !== False)
				       return $language['code'];	
			}
		}

		return false;
	}
}

?>
