<?php
namespace misc;

class Cache {

	private $cm;
	private $host_prefix;

	/*
	 * Constructor
	 *    
	 * Params:
	 *  
	 *   cm - The environment variable
	 */
	public function __construct($cm) {

		if (get_class($cm) != 'Environment') {
			throw new \Exception('Cache: Not given a valid environment in constructor');
		}

		$this->cm = $cm;

		// Create a host prefix 
		//
		// If we have multiple versions of the site running
		// on one server, this will prevent their variables
		// from overlapping
		//
		$prefix = substr(md5($this->cm->hostname), 0, 3);
		$this->host_prefix = $prefix . '_';
	}

	/*
	 * Adds a variable to the apc cache
	 *
	 * Params:
	 *
	 *   key - the name under which the searched value is stored
	 *
	 * Returns:
	 *   
	 *   - The value if it was found
	 *   - False, if no value was found
	 */
	public function fetch($key) {

		$real_key = $this->host_prefix . $key;

		$result = apc_fetch($real_key, $success);

		// Not sure what result returns if nothing is found
		//   the documentation is unclear
		//
		//   So we'll just have this if statement here
		//
		if ($success == True)
			return $result;
		else
			return False;
	}

	/*
	 * Adds a variable to the apc cache
	 *
	 * Params:
	 *
	 *   key - the name under which the value will be stored
	 *   value - the value; may be string, number, array, or object
	 *   ttl - time to live (seconds); default is 0;  if ttl == 0, value will persist until removed
	 *   overwrite - if true, will overwrite if key is already present in cache
	 *
	 */
	public function add($key, $value, $ttl, $overwrite=True) {

		$real_key = $this->host_prefix . $key;

		if ($overwrite === True) {
			return apc_store($real_key, $value, $ttl);
		}
		else {
			return apc_add($real_key, $value, $ttl);
		}

	}
}

?>
