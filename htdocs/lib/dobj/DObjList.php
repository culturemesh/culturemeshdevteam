<?php
namespace dobj;

class DObjList implements \Countable, \Iterator, \ArrayAccess {

	private $dlist = array();
	private $position = 0;
	private $m;

	private $ulhtml;
	private $lihtml;
	private $ulid;
	private $ulclass;

	public function __construct() {

		// just makin' sure
		$this->position = 0;
	}

	public function dInsert($item) {

		if (!$item instanceOf DObj) {
			throw new \InvalidArgumentException('Must insert type DObj.');
		}

		array_push($this->dlist, $item);

		return true;
	}

	/////// DISPLAY FUNCTIONS ///////////
	/// give a mustache engine
	public function setMustache($m) {
		$this->m = $m;
	}

	public function setUlId($ulid) {
		$this->ulid = $ulid;
	}

	public function setUlClass($ulclass) {
		$this->ulclass = $ulclass;
	}

	public function getHTML($context, $vars) {

		$displayable = false;

		// check if the item is displayable
		// by checking first item in list
		if (count($this->dlist) > 0) {
			$first_thing = $this->dlist[0];
			if (method_exists($first_thing, 'getHTML')) {
				$displayable = true;
			}
			else
			 { throw new \Exception(get_class($first_thing) . ' has no method, \'getHTML\''); }
		}
		else 
		 { throw new \Exception('This list is empty'); }

		 if ($displayable) {

			 $this->li_html = array();

			 foreach ($this->dlist as $obj) {

				// what now?
				$html = $obj->getHTML($context, $vars);

				array_push($this->li_html, $html);
			 }

			 return $this->li_html;
		 }
	}

	/*
	 * transforms array into sectioned list
	 */
	public function splits($property) {

		$splits = array();

		foreach ($this->dlist as $obj) {
			$p = $obj->getSplit($property);

			// can simplify this section later
			// split on object

			// blank to string
			if (is_object($p))
			  $s = $p->standOut();
			else
			  $s = $p;

			// check for existing value
			$found = false;
			foreach ($splits as $split) {

				// add to the thing
				if (isset($split['key']) && $split['key'] == $s) {
					$found = true;
					break;
				}
			}

			if ($found)
   			  array_push($split['array'], $obj);

			// make new split
			else {
				array_push($splits, array(
					'array' => array($obj),
					'section' => $p,
					'key' => $s)
				);
			}
		}

		$sl = new SectedDObjList();
		$sl->slist = $splits;
		return $sl;
	}
	

	//////////// THINGS I MUST SET ////////////////
	public function count() {

		return count($this->dlist);
	}

	public function sort($key, $asc) {

	}

	public function me() {
		return $this->dlist;
	}

	// return current thing
	function current() {
		return $this->dlist[$this->position];
	}	

	// return key of current element
	function key() {
		return $this->position;
	}

	// move forward by one
	function next() {
		$this->position += 1;
	}

	// reset at zero
	function rewind() {
		$this->position = 0;
	}

	// checks if current position is valid
	function valid() {
		return isset($this->dlist[$this->position]);
	}

	public function offsetSet($offset, $value) {
		if (is_null($offset)) {
			$this->dlist[] = $value;
		} else {
			$this->dlist[$offset] = $value;
		}
	}

	public function offsetExists($offset) {
		return isset($this->dlist[$offset]);
	}

	public function offsetUnset($offset) {
		unset($this->dlist[$offset]);
	}

	public function offsetGet($offset) {
		return isset($this->dlist[$offset]) ? $this->dlist[$offset] : null;
	}
}

?>
