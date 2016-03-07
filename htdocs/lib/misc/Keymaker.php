<?php
namespace misc;

class Keymaker {

	private $files_loaded;
	private $input;

	private $file_dir;

	private $file_opener = "keymaker-";

	private $prefixes;
	private $suffixes;
	private $prepositions;

	/*
	 * Nothing much happens in this constructor
	 *  The file gets loaded...pretty simple
	 */
	public function __construct($file_accessor, $input=NULL) {

		$filename = NULL;

		$this->files_loaded = False;

		//
		// LOAD KEYWORDS
		// //
		if (get_class($file_accessor) === 'Environment') {
			$this->file_dir = $file_accessor::site_root() . $file_accessor->ds . 'data' . $file_accessor->ds;
		}
		else if (is_string($file_accessor)) {
			$this->file_dir = $file_accessor;
		}
		else {
			throw new \Exception('Keymaker->__construct:: Must pass a valid way to find the directory, the directory itself, or the Environment object');
		}
	}

	/*
	 * Returns the keys
	 *
	 * 1) Fullname
	 * 2) Keywords
	 * 3) Smush keywords w/implosion
	 *
	 */
	public function generateKeys($input, $type=NULL) {

		// initial metaphone call
		//   to get full name
		$keys = $this->callMetaphone($input);

		// 
		if ($this->hasSpaces($input)) {

			// Will probably be calling this more than once
			//   want to avoid loading files if I can
			if (!$this->files_loaded) {
				$this->loadFiles();
			}

			// remove parenthetical

			$additional_strings = $this->processString($input, $type);

			foreach ($additional_strings as $string) {

				// Merge both arrays,
				//   but check that original string isn't duplicated
				//
				if ($string !== $input) {

					$more_keys = $this->callMetaphone($string);

					// Check for duplicates
					//   and move right along
					//
					foreach($more_keys as $key) {

						if (!in_array($key, $keys)) {
						  array_push($keys, $key);
						}
					}
				}
			}
		}

		return $keys;
	}

	/*
	 * Calls metaphone and shhhhhhhh
	 *   also returns enumerated array
	 */
	private function callMetaphone($input) {

		$keys_raw = \misc\Util::DoubleMetaphone($input);

		$keys = $this->enumerateArray( $keys_raw );

		return $keys;
	}

	/*
	 * Returns array with numerically indexed keys
	 */
	private function enumerateArray( $input_array ) {
		return array_filter( array_values( $input_array ));
	}

	/*
	 * Checks to see if string has spaces
	 */
	private function hasSpaces($input) {
		return strpos($input, ' ') !== False;
	}

	/*
	 * Creates a list of strings
	 *
	 * 1) Unique terms 
	 * 2) Implosion of unique terms w/o removable keywords
	 *
	 * ** Removes parentheses **
	 *
	 */
	private function processString( $input, $type=NULL ) {

		//
		// string list for
		//
		$total_list = array();
		$string_list = array();

		// take care of forward slashes and parentheses
		$cleaner_input = str_replace(array(' / ', '/'), ' ', $input);

		// explode string
		$explosion = explode(' ', $cleaner_input);

		$first_term = $explosion[0];
		$last_term = $explosion[ count($explosion) - 1 ];

		$middle_term_count = count($explosion) - 2;

		//
		//// BEGIN THE CHECKS
		//

		// first item
		if (!isset($this->prefixes[$first_term])) {
			array_push($string_list, $first_term);
		}

		// middle items
		for ($i = 1; $i <= $middle_term_count; $i++) {

			$term = $explosion[$i];

			// check for preposition
			if (!isset($this->prepositions[$term])) {
				array_push($string_list, $term);
			}
		}

		// last, because order is important
		if (!isset($this->suffixes[$last_term])) {
			array_push($string_list, $last_term);
		}

		// implode string for final value
		if (count($string_list) > 1) {
		  array_push( $string_list, implode(' ', $string_list) );
		}

		return array_merge($total_list, $string_list);
	}

	/*
	 * Loads the prefix, suffix, and prepositions
	 * for use in KEYMAKINNNNNNNN
	 */
	private function loadFiles() {
	
		$info_array = array(
			array(
				'file_name' => $this->file_opener . 'prefixes.txt',
				'array_name' => 'prefixes'
			),
			array(
				'file_name' => $this->file_opener . 'suffixes.txt',
				'array_name' => 'suffixes'
			),
			array(
				'file_name' => $this->file_opener . 'prepositions.txt',
				'array_name' => 'prepositions'
			)
		);

		foreach ($info_array as $info) {

			// open file
			$handler = fopen($this->file_dir . $info['file_name'], "r");

			if (!$handler) {
				throw new \Exception('Keymaker: Couldn\'t open file for reading');
			}

			// initialize array
			$this->$info['array_name'] = array();

			// Read file line by line
			//
			// fgets - reads one line from file
			// feof - checks for end of file
			//
			while(!feof($handler)) {
				$string = fgets($handler);
				array_push( $this->$info['array_name'], str_replace(array("\r\n", "\r"), "", $string));
			}

			// rearrange array
			$this->$info['array_name'] = array_flip( $this->$info['array_name'] );

			// close file
			fclose($handler);
		}

		// Now setting files loaded to True
		$this->files_loaded = True;
		// I've just set files loaded to True
	}

	private function filesLoaded() {
		return $this->files_loaded;
	}

	/*
	 * Putting this here in case I need it later...
	 */
	private function handleParentheses() {

		//
		// Take care of parenthetical value
		//

		/*
		$forward_pos = strpos($input, '(');
		if ($forward_pos !== False) {

			$inner_string = NULL;

			// get position of last parenthesis
			$end_pos = strpos($input, ')');

			// 
			if ($end_pos === False) {

				$inner_string = substr($input, $forward_pos+1, ($end_pos - $forward_pos));

				// remove parentheses from string
				$input = substr_replace($input, '', $forward_pos+1, ($end_pos - $forward_pos));
			}
			else {
				$inner_string = substr($input, $forward_pos);

				// remove parentheses from string
				$input = substr_replace($input, '', $forward_pos+1);
			}

			array_push($total_list, $inner_string);

			$cleaned_input = str_replace(array('(', ' (', ')', ') '), '', $cleaner_input); 
		}
		 */
	}
}

?>
