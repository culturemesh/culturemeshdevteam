<?php
namespace dobj;

class SectedDObjList extends DObjList {

	protected $slist;

	// returns the number of sections
	public function count() {
		return count($this->slist);
	}

	public function countAll() {

		$count = 0;
		$keys = array_keys($this->slist);

		// get counts for all arrays
		foreach ($keys as $key) 
		  $count += count( $this->slist[$key] );

		return $count;
	}

	public function getHTML($context, $vars) {

		$displayable = false;

		if (count($this) > 0) {
			$displayable = true;
		}

		if ($displayable) {

			$mustache = $vars['mustache'];
			$cm = $vars['cm'];
			$template = $vars['list_template'];

			// get html for individual elements
			$this->li_html = array();

			for ($i = 0; $i < count($this->slist); $i++) {

				$li_html = array();
				foreach ($this->slist[$i]['array'] as $obj) {
				  array_push($li_html, $obj->getHTML($context, $vars));
				}

				$this->slist[$i]['li_html'] = $li_html;
			}

			return $mustache->render($template, array(
				 'vars' => $cm->getVars(),
				 'list' => $this->slist
				 )
			);
		}
		else
		  throw new \Exception('This list is empty');
	}
}

?>
