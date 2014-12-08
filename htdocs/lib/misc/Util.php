<?php
namespace misc;

class Util {

	/*
	 * hasStringKey()
	 *  - Checks if an array has string keys
	 */
	public static function hasStringKey($array) {
  		return (bool)count(array_filter(array_keys($array), 'is_string'));
	}

	public static function getController($string) {
		$split = explode('#', $string);

		if (count($split) != 2) {
			throw new \Exception("{$string} is not a valid control string");
		}

		return array(
			'controller' => $split[0],
			'action' => $split[1]
		);
	}
}

?>
