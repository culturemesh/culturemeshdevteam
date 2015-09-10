<?php
namespace misc;

/*
 * A Log class for recording errors
 */
class Log {

	private $cm;
	private $log_path;

	public function __construct( $cm, $file_path='error.log' ) {

		if (!isset($cm))
			throw new \Exception('Log: Environment variable not set!');

		$this->cm = $cm;

		// set log path
		ini_set("error_log", \Environment::$site_root . $cm->ds . $file_path);
	}

	/*
	 * Writes a string into the error log
	 */
	public function logMessage( $msg ) {
		error_log( $msg );
	}

	/*
	 * Converts an array into a string so that it can be
	 * smushed into an error log
	 */
	public function logArray( $array ) {
		error_log(print_r( $array, true ));
	}

	/*
	 * Converts an object into a string so that it can be
	 * smushed into an error log
	 */
	public function logVar( $var ) {

		$contents = var_export($var, true);
		error_log($contents);
	}
}

?>
