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
	private $filter_retweets = '-filter:retweets';

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
	private $op_and = '%20';
	private $op_l_parenthesis = '%28';
	private $op_r_parenthesis = '%29';

	private $until_assignment = '&until=';
	private $since_assignment = '&since=';

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

			if (get_class($network) != 'dobj\Network') {
				throw new \Exception('TwitterQuery: Cannot build a query, was not passed a dobj\Network');
			}

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

		$this->origin_scope = $network->query_origin_scope;
		$this->location_scope = $network->query_location_scope;
		$this->query_level = $network->query_level;

		// get origin
		$raw_origin = $network->getQueryOriginComponent();
		$origin_component = $this->explodeSlash($raw_origin);
		$origin_arg = $this->prepareComponent($origin_component);

		// get current location
		$raw_location = $network->getQueryLocationComponent();
		$location_arg = $this->prepareComponent($raw_location);

		$this->addComponents($network, $origin_arg, $location_arg, $this->query_level);

		$this->addResultType('mixed');
		$this->addSinceDate($network);

		// record query variables
		$this->origin_arg = $origin_arg;
		$this->location_arg = $location_arg;
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
				'hashtag' => NULL,
				'hashtag_raw' => NULL
			);

			// check for parentheses
			$return_component['raw'] = $component_arg;
			$return_component['basic'] = $this->addQuotes( $this->removeParentheses( $component_arg) );

			// check if hashable
			$hashable = $this->determineStringHashability($component_arg);

			if ($hashable) {
				$return_component['hashtag'] = NULL;
			}
			else {
				$hashtag_array = $this->makeHashtag($component_arg);
				$return_component['hashtag_raw'] = $hashtag_array['raw'];
				$return_component['hashtag'] = $hashtag_array['signed'];
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
	private function addArg($arg, $link) {

		$arg_string = $this->op_l_parenthesis;

		if (isset($arg[0])) {

			for($i = 0; $i < count($arg); $i++) {

				$a = $arg[$i];

				$arg_string .= $this->getArg($a, $link);

				if ((count($arg) - $i) > 1)
					$arg_string .= $link;
			}
		}
		else {
			$arg_string .= $this->getArg($arg, $link);
		}

		$arg_string .= $this->op_r_parenthesis;
		$this->query .= $arg_string;
	}

	private function getArg($arg, $link) {

		$arg_string = '';

		if ($arg['hashtag'] !== NULL ) {
			$arg_string .= $arg['hashtag'];
			$arg_string .= $link;
		}

		$arg_string .= $this->replaceSpace( $arg['basic'] );

		return $arg_string;
	}

	/*
	 * Hopefully this will be the last version of this
	 * ...at least for a while
	 *
	 */
	private function addComponents($network, $origin, $location, $level) {

		if ($level == 1) {
			$component_link = $this->op_and;
			$link_term = $this->op_and;
		}
		if ($level == 2) {
			$component_link = $this->op_and;
			$link_term = $this->op_space . $this->op_or . $this->op_space;
		}
		if ($level == 3) {
			$component_link = $this->op_space . $this->op_or . $this->op_space;
			$link_term = $this->op_space . $this->op_or . $this->op_space;
		}

		// special language considerations
		if ($network->network_class == '_l') {

			// check for the language code
			$language_code = $this->getLanguageCode($origin);

			if ($language_code === False || isset($origin[0])) {
				$this->addArg($origin, $link_term);
				$this->query .= $component_link;
			}
		}
		else {

			// add origin
			$this->addArg($origin, $link_term);
			$this->query .= $component_link;
		}


		// add location
		$this->addArg($location, $link_term);

		// do this next thing as well
		$this->filterRetweets();

		// add language code if kosher
		if (isset($language_code) && $language_code !== False) {
			$this->addLanguageTerm($language_code);
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
		$paren_pos = strpos($term_arg, '(');
		if ($paren_pos !== False) {
			preg_match('#\((.*?)\)#', $term_arg, $clay);

			// get value between parentheses
			$clay = $clay[1];
		}
		else {
			$clay = $term_arg;
		}

		if (strpos($clay, ' ') === False) {

			return array(
				'raw' => $clay,
				'signed' => $this->op_hashtag . $clay
			);
		}
		
		$clay = $this->removeSpace($clay);

		return array(
			'raw' => $clay,
			'signed' => $this->op_hashtag . $clay
		);
	}

	/*
	 * Adds a hashtag only if 
	 */
	private function addHashtag($term_arg, $link) {

		// check to see if this is the first query argument
		// if it isn't, adds or and a space
		//
		$end = substr($this->query, -3);

		if ($end !== '?q=') {
			//$this->query .= $this->op_space . $this->op_or . $this->op_space;
			//
			//$this->query .= $link;
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
	 *
	 * Returns true if it doesn't need modification
	 * REturns false if work must be done
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

	private function removeParentheses($string) {

		$i = strpos($string, '(');
		if ($i !== False) {
			return substr($string, 0, $i-1);
		}
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

	private function filterRetweets() {

		$this->query .= $this->op_space . $this->filter_retweets;
	}

	/*
	 * Adds until date to query
	 *
	 */
	private function addUntilDate() {

		$this->query .= $this->until_assignment . $this->until_date;
	}

	/*
	 * Adds since date to query
	 *
	 */
	private function addSinceDate($network) {

		$this->query .= $this->since_assignment . $network->query_since_date;
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
	 * Returns the since date that you have set in this
	 * lovely li'l class here
	 *
	 * @returns - since_date
	 */
	public function getSinceDate() {
		return $this->since_date;
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
	 *
	 * Side Effect: May remove an element of target_lingo if
	 * it's language code has been found
	 *
	 */
	private function getLanguageCode(&$target_lingo) {

		// else, check out the json file
		$languages = file_get_contents('lib/api/twitter-languages.json');
		$languages_json = json_decode($languages, true);

		if (isset($target_lingo[0]) && !is_string($target_lingo)) {

			$result_array = array();

			foreach ($target_lingo as $lingo) {

				// must make variable, or error thrown
				$raw_lingo = $lingo['raw'];

				$result = $this->getLanguageCode($raw_lingo);

				if ($result !== False)
					array_push($result_array, $result);
			}

			// Twitter only takes one language at a time,
			// so return the first correct value
			// ...until we have something better
			// 
			if (count($result_array) == 0)
				return false;
			else if (count($result_array) > 1) {

				// remove first item, since it'll be the language
				unset($target_lingo[0]);
				$target_lingo = array_values($target_lingo);

				return $result_array[0];
			}
			else
				return $result_array[0];
		}
		else {

			if (is_string($target_lingo)) {

				// check the language array
				$lang_array_value = $this->language_table[$target_lingo];

				if ($lang_array_value !== NULL)
					return $lang_array_value;

				foreach($languages_json as $language) {

					if (strpos($language['name'], $target_lingo) !== False)
					       return $language['code'];	
				}
			}
			else {

				// check the language array
				$lang_array_value = $this->language_table[$target_lingo['raw']];

				if ($lang_array_value !== NULL)
					return $lang_array_value;

				foreach($languages_json as $language) {

					if (strpos($language['name'], $target_lingo['raw']) !== False)
					       return $language['code'];	
				}
			}
		}

		return false;
	}

	/*
	 *
	 * @param - $origin
	 * @param - $location
	 * @param - $level
	 *
	 */
	private function linkTerms() {

		switch ($level) {

		case 0:
			// all ANDS
			break;

		case 1:
			// OR between hashes
			break;

		case 2:
			// OR between origin and location
			break;
		default:
			// all ANDS
		}
	}

	/*
	 * Returns all the terms of the query as an array
	 *
	 */
	public function getTerms() {

		$terms = array();

		// location terms
		//
		array_push($terms, $this->location_arg['raw']);

		if ($this->location_arg['hashtag_raw'] != NULL)
			array_push($terms, $this->location_arg['hashtag_raw']);

		// origin terms
		//
		// check for array
		//
		if (isset($this->origin_arg[0])) {

			foreach ($this->origin_arg as $arg) {

				array_push($terms, $arg['raw']);

				if ($arg['hashtag_raw'] != NULL)
					array_push($terms, $arg['hashtag_raw']);
			}
		}
		else {

			array_push($terms, $this->origin_arg['raw']);

			if ($this->origin_arg['hashtag_raw'] != NULL)
				array_push($terms, $this->origin_arg['hashtag_raw']);
		}

		return $terms;
	}
}

?>
