<?php
namespace search;

class SearchManager {

	protected $cm;
	protected $dal;
	protected $do2db;
	protected $search;

	protected $search_results;

	public function __construct($cm, $dal, $do2db, $search) {

		$this->cm = $cm;
		$this->dal = $dal;
		$this->do2db = $do2db;
		$this->search = $search;
	}


	public function getResults() {

		$this->search_results = $this->search->run($this->dal, $this->do2db);

		// process results however that happens

		return $this->search_results;
	}

	private function parseInput() {

	}

	private function searchDB() {

	}

	private function processResults() {

	}
}

?>
