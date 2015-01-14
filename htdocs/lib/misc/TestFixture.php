<?php
namespace misc;

class TestFixture {

	private $fixtures;
	private $keys;
	private $prefix = 'cmdata-';
	private $suffix = '.xml';

	public function __construct($fixtures=NULL) {

		$err_string = 'Must pass in array(<table_name> => NULL | <file> | [<file_1>, <file_2>])';

		if ($fixtures == NULL) {
			throw new \InvalidArgumentException($err_string);
		}

		if (!Util::hasStringKey($fixtures)) {
			throw new \InvalidArgumentException($err_string);
		}

		$this->keys = array_keys($fixtures);

		// test arrays for stuff or nothing
		foreach ($fixtures as $key => $thing) {

			if (!is_array($thing)) {
				throw new \InvalidArgumentException($err_string);
			}
		}


		$this->fixtures = $fixtures;

	}

	public function getFileNames() {

		$files = array();

//		var_dump($this->fixtures);
		foreach ($this->fixtures as $table_name => $fs) {

//			$fn = DIRECTORY_SEPARATOR.$table_name.DIRECTORY_SEPARATOR.$this->prefix;

			if (count($fs) == 0) {
				$fn = DIRECTORY_SEPARATOR.$table_name.DIRECTORY_SEPARATOR.$this->prefix.$table_name.$this->suffix;
				array_push($files, $fn);
				break;
			}

			foreach ($fs as $f) {

				//$fn .= $f.$this->suffix;
				$fn = DIRECTORY_SEPARATOR.$table_name.DIRECTORY_SEPARATOR.$this->prefix.$f.$this->suffix;
				array_push($files, $fn);
			}

			/*
			if (is_array($file_id)) {

				if (count($file_id) == 0) {
					$fn = DIRECTORY_SEPARATOR.$table_name.DIRECTORY_SEPARATOR.$this->prefix.$table_name.$this->suffix;
					array_push($files, $fn);
					break;
				}

				foreach ($file_id as $f) {
					$fn = DIRECTORY_SEPARATOR.$table_name.DIRECTORY_SEPARATOR.$this->prefix.$f.$this->suffix;
					array_push($files, $fn);
				}
			}
			else {
				// assume that table_name should
				// be the same as file_id
				if ($file_id == NULL) {
					$fn = DIRECTORY_SEPARATOR.$table_name.DIRECTORY_SEPARATOR.$this->prefix.$table_name.$this->suffix;
					array_push($files, $fn);
				}
				else {
					$fn = DIRECTORY_SEPARATOR.$table_name.DIRECTORY_SEPARATOR.$this->prefix.$table_name.$this->suffix;
					array_push($files, $fn);
				}
			}
			 */
		}

		return $files;
	}

	public function __get($name) {

		// stop the thing
		if (!isset($this->$name)) {
			throw new \InvalidArgumentException('Fixture doesn\'t have that parameter');
		}

		if ($name == 'fixtures') {
			throw new \InvalidArgumentException('Direct access to fixtures not allowed');
		}

		return $this->$name;
	}
}
?>
