<?php
namespace dobj;

class TweetQueryAdjustment extends DObj {

	protected $id_network;
	protected $start_level;
	protected $target_level;
	protected $origin_scope_start;
	protected $origin_scope_end;
	protected $location_scope_start;
	protected $location_scope_end;
	protected $start_since_date;
	protected $end_since_date;
	protected $prev_query_relevance;

	public function insert($dal, $do2db) {

		$result = $do2db->execute($dal, $this, 'insertTweetAdjustment');
	}

	/*
	 * Parses adjustment, stores changes in this object,
	 * and passes them onto the network
	 */
	public function processAdjustment(&$network, $adjustment) {

		// get network id
		$this->id_network = $network->id;

		$this->start_since_date = $network->query_since_date;
		$this->start_level = $network->query_level;
		$this->origin_scope_start = $network->query_origin_scope;
		$this->location_scope_start = $network->query_location_scope;

		// Parse adjustment
		if (is_int($adjustment)) {

			$scope_shift_count = (int) $adjustment / 3;
			$this->target_level = $adjustment % 3;
			$this->origin_scope_end = $this->origin_scope_start;
			$this->location_scope_end = $this->location_scope_start;

			for ($i = 0; $i < $scope_shift_count; $i++) {
				
				// shift location before origin
				//
				if ($this->location_scope_end > 1) {
					
					$this->location_scope_end--;
				}
				else {

					if ($this->origin_scope_end > 1) {

						$this->origin_scope_end--;
					}
				}
			}
		}

		if (is_array($adjustment)) {
			
			$this->target_level = $adjustment['query_level'];
			$this->origin_scope_end = $adjustment['query_origin_scope'];
			$this->location_scope_end = $adjustment['query_location_scope'];
			$this->end_since_date = $adjustment['query_since_date'];
		}

		$network->extend($this->getAdjustment());
	}

	/*
	 * Returns the adjustments made for network extension
	 * if an end property is NULL, pass the start property to the
	 * network
	 *
	 * @returns - array
	 *
	 */
	public function getAdjustment() {

		$adjustment = array();

		// since date
		if ($this->end_since_date == NULL)
			$adjustment['query_since_date'] = $this->start_since_date;
		else
			$adjustment['query_since_date'] = $this->end_since_date;

		// origin scope
		if ($this->origin_scope_end == NULL)
			$adjustment['query_origin_scope'] = $this->origin_scope_start;
		else
			$adjustment['query_origin_scope'] = $this->origin_scope_end;

		// location scope
		if ($this->location_scope_end == NULL)
			$adjustment['query_location_scope'] = $this->location_scope_start;
		else
			$adjustment['query_location_scope'] = $this->location_scope_end;

		// level
		if ($this->target_level == NULL)
			$adjustment['query_level'] = $this->start_level;
		else
			$adjustment['query_level'] = $this->target_level;

		return $adjustment;
	}
}

?>
