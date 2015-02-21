<?php
namespace nav;

$pages = array('network', 'index', 'search_results');

class HTTPRedirect {

	private $page_names;
	private $url_fragment;
	private $url_query;
	private $url_host;
	private $url_path;
	private $url_control;
	private $control;
	private $value;

	private $redirect_url;
	protected $cm;

	// takes a url and separates it into component parts
	// makes path relative
	function __construct($cm, $raw_url, $page_names) {
		
		// environment
		$this->cm = $cm;

		// pagenames for right now
		$this->page_names = $page_names;

		// parse raw url
		$parsed_url = parse_url($raw_url);

		// get stuff to fill url parts
		$this->url_host = $parsed_url['host'];
		$this->url_path = self::extractRelativePath($parsed_url['path']);

		// control stuff
		$cv = self::parseControl();

		if (isset($cv)) {
			$this->url_path = '';
			$this->url_control = $cv['control'] . '/' . $cv['value'];
		}

		// add query string
		if (isset($parsed_url['query']) && strlen($parsed_url['query']) > 0)
			$this->url_query = '?'.$parsed_url['query'];
		else
			$this->url_query = '';

		// add fragment
		if (isset($parsed_url['fragment']))
			$this->url_fragment = $parsed_url['fragment'];
	}

	// extracts the relative path from the parse_url path
	private function extractRelativePath($path) {

		// search through all the page names,
		// look for the goodness within
		foreach ($this->page_names as $page) {

			// try and get string position
			$pos = strpos($path, $page);

			if ($pos == false) {
				continue;
			}
			else {
				// return triumphant
				return substr($path, $pos);
			}
		}

		// if we haven't found anything, it's because
		// we're at the root
		return 'index.php';
	}

	public function getPath() {
		return $this->url_path;
	}

	private function parseControl() {

		$cv = array(
			'control' => NULL,
			'value' => NULL
		);

		$fragments = explode('/', $this->getPath());

		if (count($fragments) == 1)
			// we could have index, or something else
		{}
		else {
			$this->control = $fragments[0];
			$this->value = $fragments[1];

			return array(
				'control' => $this->control,
				'value' => $this->value
			);
		}
	}

	public function setControl($control, $value) {

		// set url path to /
		$this->url_path = '';

		$this->control = $control;
		$this->value = $value;

		$this->url_control = $this->control.'/'.$this->value.'/';
	}

	public function getControl() {

		return array(
			'control' => $this->control,
			'value' => $this->value
		);
	}		

	// add a key value to query string
	public function addQueryParameter($key, $value) {

		// check if query is empty
		if (strlen($this->url_query) == 0) {
			$this->url_query = '?'.$key.'='.$value;
		}
		else {
			$this->url_query .= '&'.$key.'='.$value;
		}
	}


	// add assoc_array of key->values to query string
	public function addQueryParameters($params) {

		// split into 
		foreach($params as $key => $value) {
			self::addQueryParameter($key, $value);
		}
	}

	// removes a key, value pair from querystring
	// uses key to figure out which is which
	public function removeQueryParameter($key) {

		$query = $this->url_query;
		// if there is nothing to delete,
		// return false
		if (strlen($this->url_query) == 0) {
			return false;
		}
		else {
			$pos = strpos($query, $key);

			if ($pos > -1) {
				
				// set counter = to last char of first occurrence
				$i = $pos + strlen($key);

				// proceed to look for the end
				//  the end could be an ampersand, or it could be
				//  the end of the string
				while ($query[$i] != '&' && $i < strlen($query)) {
					$i++;
				}

				// if we're at the end of the string,
				// return all up until pos
				if ($i == strlen($query)) {
					// decrement pos, to delete char beforehand
					$pos--;
					// operation
					$this->url_query = substr_replace($query, '', $pos);
				}
				// else return everything up to and including &
				else {
					// increment i for correct length
					$i++;
					// include length
					$this->url_query = substr_replace($query, '', $pos, $i - $pos);
				}

				// done
				return true;
			}
			else  {
				// done
				return false;
			}
		}
	}

	public function removeQueryParameters($params) {
		foreach ($params as $param) {
			self::removeQueryParameter($param);
		}
	}


	// lets a user figure out what a path fragment is
	public function pathContains($path) {
		if (substr_count($this->url_path, $path) > 0)
			return true;
		else
			return false;
	}

	// change redirect path
	public function setPath($path) {
		$this->url_path = $path;

		// clear query and fragment
		$this->url_query = null;
		$this->url_fragment = null;
	}

	public function setFragment($fragment) {
		$this->url_fragment = '#'.$fragment;
	}

	// return redirect url
	public function getUrl() {
		return $this->url_path.$this->url_control.$this->url_query.$this->url_fragment;
	}

	public function getExecute() {
		return '//'. $this->cm->hostname. $this->cm->ds . self::getUrl();
	}

	// if you want, take care of the whole header call right here
	public function execute() {
		header('Location: //'. $this->cm->hostname. $this->cm->ds . self::getUrl());
	}
}

?>
