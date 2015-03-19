<?php
namespace dobj;

class SectedDObjList extends DObjList {

	// Format of an element in slist
	//
	// slist[n] = array(
	// 	'array' => { DObjList || SectedDObjList}
	// 	'key' => the type, usually the property of the object (eg event date)
	// 	'section' => the grouping, if key is event date, section is Jan 1st 2014
	//
	protected $slist;

	// returns the number of sections
	public function count() {
		return count($this->slist);
	}

	// return a count of all elements in the structure
	public function countAll() {

		$count = 0;

		for ($i = 0; $i < count($this); $i++) {

			if ($this->slist[$i]['array'] instanceof SectedDObjList) {
			  $count += $this->slist[$i]['array']->countAll();
			}
			else
			  $count += count($this->slist[$i]['array']);
		}

		return $count;
	}

	// return a count of all elements of a specific key
	public function countSection($key, $section) {

		$count = 0;

		for ($i = 0; $i < count($this); $i++) {

			// decide between count and countall
			if ($this->slist[$i]['array'] instanceof SectedDObjList) {


				// check key and section
				//
				// count all if this list matches key and section
				//
				// if not, check child lists for matches
				if ($this->slist[$i]['key'] == $key &&
				    $this->slist[$i]['section'] == $section) {


					$count += $this->slist[$i]['array']->countAll();
				}
				else {
					$count += $this->slist[$i]['array']->countSection($key, $section);
				}
			}
			else {

				// check key and section
				if ($this->slist[$i]['key'] == $key &&
				    $this->slist[$i]['section'] == $section) {

					$count += count($this->slist[$i]['array']);
				}
			}
		}

		return $count;
	}

	public function getList() {
		return $this->slist;
	}

	public function getHTML($context, $vars) {

		$displayable = false;

		$list_vars = array();

		// turn off sectioning if class display
		// mode is activated
		//
		if ($this->display_mode == 'class')
		  $list_vars['section_header'] = False;
		else if ($this->display_mode == 'none')
		  $list_vars['section_header'] = False;
		else
		  $list_vars['section_header'] = True;

		// check if it's nested (ie called recursively)
		if (isset($vars['nested']) && $vars['nested'] === True)
		  $list_vars['nested'] = True;

		if (count($this) > 0) {
			$displayable = true;
		}

		$x = count($this);

		if ($displayable) {

			$mustache = $vars['mustache'];
			$cm = $vars['cm'];

			if (isset($vars['max']))
			  $max = $vars['max'];
			else
			  $max = 999999;

			$template = $vars['list_template'];

			// get html for individual elements
			$this->li_html = array();

			for ($i = 0, $total = 0; $i < count($this); $i++) {

				if ($max) {
					if ($total >= $max)
						break;
				}

				$li_html = array();
				$thing = $this->slist[$i];
				$arr = $this->slist[$i]['array'];

				if ($arr instanceof SectedDObjList) {
					
					$vars['nested'] = true;
					$this->slist[$i]['li_html'] = $arr->getHTML($context, $vars);
				}
				else {

					foreach ($arr as $obj) {
						if ($total >= $max)
							break(2);

						// add class to var things
						if ($this->display_mode == 'class')
						  $vars['item_class'] = $this->slist[$i]['section'];

						array_push($li_html, $obj->getHTML($context, $vars));
						$total++;
					}

					$this->slist[$i]['li_html'] = $li_html;
				}
			}

			return $mustache->render($template, array(
				 'vars' => $cm->getVars(),
				 'list_vars' => $list_vars,
				 'list' => $this->slist
				 )
			);
		}
		else
		  throw new \Exception('This list is empty');
	}

	/*
	 * Orders list by section
	 *
	 *  Params
	 *    target_key - the name of the key that we wish to order by
	 *    arrangement - how we wish to order
	 *       possible values:
	 *         - 'asc' : ascending order
	 *         - 'desc' : descending order
	 *         - associative array : 
	 *            keys => the key here represents the weight of the value,
	 *                lower values are given priority
	 *            value => the value is the value of the section
	 *
	 *  Returns
	 *    True - if list was ordered successfully
	 *    False - if key was not found
	 */
	public function order($target_key, $arrangement) {

		if ($this->slist[0]['key'] == $target_key) {

			// we have the array (this), now we must arrange the sections
			//
			// note: sections are always grouped together,
			// may need to get multiple indices and swap them out
			//
			// sort where a and b are sected dobj elements
			//   : ie, they have keys and sections
			//
			usort($this->slist, function($a, $b) use ($arrangement) {

				if ($arrangement == 'asc') {

					if ($a['section'] > $b['section'])
					  return 1;
					else if ($a['section'] == $b['section'])
					  return 0;
					else
					  return -1;
				}

				if ($arrangement == 'desc') {

					if ($a['section'] < $b['section'])
					  return 1;
					else if ($a['section'] == $b['section'])
					  return 0;
					else
					  return -1;
				}

				if (is_array($arrangement)) {

					if ($arrangement[$a['section']] < $arrangement[$b['section']]) 
					  return 1;
					else if ($arrangement[$a['section']] == $arrangement[$b['section']]) 
					  return 0;
					else
					  return -1;
				}
			});
		}
		else {
			for ($i = 0; $i < count($this); $i++) {

				// Lotsa recursion in this here class
				//
				if ($this->slist[$i]['array'] instanceof SectedDObjList)
				   $this->slist[$i]['array']->order($target_key, $arrangement);
			}
		}

		return true;
	}

	public function dumpElement($element) {

		var_dump($this->slist[$element]['array']);
	}

	public function getListByKey($key) {

		$lists = array();

		for ($i = 0; $i < count($this->slist); $i++) {

			// get array if it's a regular dobjlist
			//
			if ($this->slist[$i] instanceof DObjList) {
				if ($this->slist[$i]['key'] == $key) {
					array_push($lists, $this->slist[$i]['array']);
				}
			}
			// dig deeper if its a sectioned dobjlist
			//
			else {
				if ($this->slist[$i] instanceof SectedDObjList) {

					$recursive = $this->slist['array']->getListByKey($key);
					if ($recursive != False)
						array_push($lists, $recursive);
				}
				// return false if nothing is there
				else
				  return false;
			}
		}

		return $lists;
	}
}

?>
