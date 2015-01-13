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

		// get counts for all arrays
		foreach ($this->slist as $list) 
		  $count += count( $list['array'] );

		return $count;
	}

	public function getList() {
		return $this->slist;
	}

	public function getHTML($context, $vars) {

		$displayable = false;

		if (count($this) > 0) {
			$displayable = true;
		}

		$x = count($this);

		if ($displayable) {

			$mustache = $vars['mustache'];
			$cm = $vars['cm'];
			$template = $vars['list_template'];

			// get html for individual elements
			$this->li_html = array();

			for ($i = 0; $i < count($this); $i++) {

				$li_html = array();
				$thing = $this->slist[$i];
				$arr = $this->slist[$i]['array'];
				foreach ($arr as $obj) {
				  array_push($li_html, $obj->getHTML($context, $vars));
				}

				$this->slist[$i]['li_html'] = $li_html;

				if ($i > 15)
					break;
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
