<?php

class CustomSelectQueryTest extends PHPUnit_Framework_TestCase {

	protected $do2db;

	public function setUp() {

		$this->do2db = new \dal\Do2Db();
	}

	public function testConstruct() {

		$query = new \dal\CustomSelectQuery();
		$this->assertEquals('dal\CustomSelectQuery', get_class($query));
	}

	public function testRetrieve() {

		$query = $this->do2db->initializeCustomQuery();
		$this->assertEquals('dal\CustomSelectQuery', get_class($query));
	}

	public function testSetValues() {

		$query = $this->do2db->initializeCustomQuery();
		$values = array(
			'name' => 'CustomQueryBasic',
			'select_rows' => array('booger', 'flort'),
			'from_tables' => array('nose'),
			'where_cols' => array(),
			'where_values' => array(),
			'order_by_table' => '',
			'group_by_table' => '',
			'value_types' => '');

		$query->setValues($values);

		$this->assertEquals($values, $query->getValues());
	}

	public function testWriteQueryBasic() {

		$query = $this->do2db->initializeCustomQuery();
		$values = array(
			'select_rows' => array('booger', 'flort'),
			'from_tables' => array('nose'),
			'order_by_table' => '',
			'group_by_table' => '',
			'order' => null,
			'upper_limit' => null,
			'lower_limit' => null);

		$query->setValues($values);

		$write_string = "SELECT booger, flort FROM nose";

		$this->assertEquals($write_string, $query->writeQueryString());
	}

	public function testWriteQuerySelectAll() {

		$query = $this->do2db->initializeCustomQuery();
		$values = array(
			'select_rows' => array(),
			'from_tables' => array('nose'),
			'order_by_table' => '',
			'group_by_table' => '',
			);

		$query->setValues($values);

		$write_string = "SELECT * FROM nose";

		$this->assertEquals($write_string, $query->writeQueryString());
	}

	public function testWriteQueryWhereClauseSingle() {

		$query = $this->do2db->initializeCustomQuery();
		$values = array(
			'select_rows' => array('booger', 'flort'),
			'from_tables' => array('nose'),
			'order_by_table' => '',
			'group_by_table' => '',
			);

		$query->setValues($values);
		$query->addAWhere('booger', '=', 'deadpool', 's');

		$write_string = "SELECT booger, flort FROM nose WHERE booger=?";

		$this->assertEquals($write_string, $query->writeQueryString());
	}

	public function testWriteQueryWhereClauseMultiple() {

		$query = $this->do2db->initializeCustomQuery();
		$values = array(
			'select_rows' => array('booger', 'flort'),
			'from_tables' => array('nose'),
			);

		$query->setValues($values);
		$query->addAWhere('booger', '=', 'deadpool', 's');
		$query->addAnotherWhere('AND', 'treadstone', '=', 'buggy', 's');
		$query->addAnotherWhere('AND', 'eke', '=', 'yoast', 's');

		$write_string = "SELECT booger, flort FROM nose WHERE booger=? AND treadstone=? AND eke=?";

		$this->assertEquals($write_string, $query->writeQueryString());
	}

	public function testWriteQueryOrderBy() {

		$query = $this->do2db->initializeCustomQuery();
		$values = array(
			'select_rows' => array(),
			'from_tables' => array('nose'),
			'order_by_table' => 'booger');

		$query->setValues($values);

		$write_string = "SELECT * FROM nose ORDER BY booger";

		$this->assertEquals($write_string, $query->writeQueryString());
	}

	public function testWriteQueryLimitOffset() {

		$query = $this->do2db->initializeCustomQuery();
		$values = array(
			'select_rows' => array(),
			'from_tables' => array('nose'),
			'limit_offset' => 10);

		$query->setValues($values);

		$write_string = "SELECT * FROM nose LIMIT 10";

		$this->assertEquals($write_string, $query->writeQueryString());
	}

	public function testWriteQueryLimitOffsetAndRowCount() {

		$query = $this->do2db->initializeCustomQuery();
		$values = array(
			'select_rows' => array(),
			'from_tables' => array('nose'),
			'limit_offset' => 10,
			'limit_row_count' => 5);

		$query->setValues($values);

		$write_string = "SELECT * FROM nose LIMIT 10,5";

		$this->assertEquals($write_string, $query->writeQueryString());
	}

	public function testWriteQueryWhereClauseMultipleSingleLikeOp() {

		$query = $this->do2db->initializeCustomQuery();
		$values = array(
			'select_rows' => array('booger', 'flort'),
			'from_tables' => array('nose'),
			);

		$query->setValues($values);
		$query->addAWhere('booger', 'LIKE', 'deadpool', 's');
		$query->addAnotherWhere('AND', 'treadstone', 'LIKE', 'buggy', 's');
		$query->addAnotherWhere('AND', 'eke', 'LIKE', 'yoast', 's');

		$write_string = "SELECT booger, flort FROM nose WHERE booger LIKE ? AND treadstone LIKE ? AND eke LIKE ?";

		$this->assertEquals($write_string, $query->writeQueryString());
	}

	public function testWriteQueryWhereClauseMultipleSingleLikeOpOrs() {

		$query = $this->do2db->initializeCustomQuery();
		$values = array(
			'select_rows' => array('booger', 'flort'),
			'from_tables' => array('nose'),
			);

		$query->setValues($values);
		$query->addAWhere('booger', 'LIKE', 'deadpool', 's');
		$query->addAnotherWhere('OR', 'treadstone', 'LIKE', 'buggy', 's');
		$query->addAnotherWhere('OR', 'eke', 'LIKE', 'yoast', 's');

		$write_string = "SELECT booger, flort FROM nose WHERE booger LIKE ? OR treadstone LIKE ? OR eke LIKE ?";

		$this->assertEquals($write_string, $query->writeQueryString());
	}

	public function testThatThingINeedThisFor() {

		$query = $this->do2db->initializeCustomQuery();
		$values = array(
			'from_tables' => array('metaphone_keys'),
			);

		$query->setValues($values);
		$query->addAWhere('meta_key', 'LIKE', 'SMFNG', 's');
		$query->addAnotherWhere('OR', 'meta_key', 'LIKE', 's');

		$write_string = "SELECT * FROM metaphone_keys WHERE meta_key LIKE ? OR meta_key LIKE ?";

		$this->assertEquals($write_string, $query->writeQueryString());
	}
}
