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
		foreach ($this->slist as $list)  {
		  $count += count( $list['array'] );
		}

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

				foreach ($arr as $obj) {
					if ($total >= $max)
						break(2);

				  	array_push($li_html, $obj->getHTML($context, $vars));
					$total++;
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
