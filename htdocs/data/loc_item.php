<?php

class LocItem {

	private $name;
	private $id;
	private $latitude;
	private $longitude;
	private $population;
	private $class;
	private $tblname;

	public $search_dists;

	function __construct($class, $tblname) {
	
		$this->class = $class;
		$this->tblname = $tblname;

		switch ($tblname) {
		case 'cities':
			$this->search_dists = array(5, 10, 20, 40, 60, 80, 100);
			break;
		case 'regions':
			$this->search_dists = array(50, 100, 200, 400);
			break;
		case 'countries':
			$this->search_dists = array(1500);
			break;
		}
	}

	public function fillFromRow($row) {

		$this->id = $row['id'];
		$this->name = $row['name'];
		$this->latitude = $row['latitude'];
		$this->longitude = $row['longitude'];
		$this->population = $row['population'];
	}

	public function getClass() {
		return $this->class;
	}

	public function setClass($class) {
		$this->class = $class;
	}

	public function getTblName() {
		return $this->tblname;
	}

	public function setTblName($tblname) {
		$this->tblname = $tblname;
	}

	public function askForAll() {
		return "SELECT * FROM {$tblname}";
	}

	public function getLatitude() {
		return $this->latitude;
	}

	public function getLongitude() {
		return $this->longitude;
	}

	public function getId() {
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}
}
