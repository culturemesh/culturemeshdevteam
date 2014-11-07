<?php
namespace dal;

/**
 * generates stub statements
 */
class StubConnection {

	public function prepare($query) {

		$ss = new StubStatement($query);
		return $ss;
	}
}

?>
