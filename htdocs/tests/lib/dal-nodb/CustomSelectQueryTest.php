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
			'where_operators' => '=',
			'where_conjunctions' => 'AND',
			'value_types' => '',
			'order_by_table' => '',
			'order' => NULL, // defaults to ASC
			'group_by_table' => '',
			'limit_offset' => NULL,
			'limit_row_count' => NULL,
			'returning_class' => 'dobj\Blank',
			'returning_list' => True,
			'params_stack' => True
			);

		$query->setValues($values);

		$this->assertEquals($values, $query->getValues());
	}

	public function testWriteQueryBasic() {

		$query = $this->do2db->initializeCustomQuery();
		$values = array(
			'name' => 'default',
			'select_rows' => array('booger', 'flort'),
			'from_tables' => array('nose'),
			'where_cols' => array(),
			'where_values' => array(),
			'where_operators' => '=',
			'where_conjunctions' => 'AND',
			'value_types' => '',
			'order_by_table' => NULL,
			'order' => NULL, // defaults to ASC
			'group_by_table' => NULL,
			'limit_offset' => NULL,
			'limit_row_count' => NULL,
			'returning_class' => 'dobj\Blank',
			'returning_list' => True
			);

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
		$this->assertTrue($query->columnCountMatchesValues());
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

	public function testInModifierString() {

		$query = $this->do2db->initializeCustomQuery();
		$values = array(
			'from_tables' => array('metaphone_keys'),
			);

		$query->setValues($values);
		$query_values =  array('this', 'that');
		$query->addAWhere('meta_key', 'IN', $query_values, 'ss', 2);

		$write_string = "SELECT * FROM metaphone_keys WHERE meta_key IN (?, ?)";

		$this->assertEquals($write_string, $query->writeQueryString());
		$this->assertTrue($query->columnCountMatchesValues());
	}

	public function testInModifierParamObject() {

		$query = $this->do2db->initializeCustomQuery();
		$values = array(
			'from_tables' => array('metaphone_keys'),
			);

		$query->setValues($values);
		$query_values =  array('this', 'that');
		$query->addAWhere('meta_key', 'IN', $query_values, 'ss', 2);

		$obj = new \dobj\Blank();
		$obj->meta_key = $query_values;

		$this->assertEquals($obj, $query->getParamObject());
	}

	public function testCreateAndAddWhere() {

		$query = $this->do2db->initializeCustomQuery();
		$values = array(
			'from_tables' => array('metaphone_keys'),
			);

		$query->setValues($values);
		$where = $query->createWhereLine('meta_key', 'IN', $query_values, NULL, 'ss', 2);
		$query->insertWhereLine($where);

		$write_string = "SELECT * FROM metaphone_keys WHERE meta_key IN (?, ?)";
		$obj = new \dobj\Blank();
		$obj->meta_key = $query_values;

		$this->assertEquals($write_string, $query->writeQueryString());
		$this->assertEquals($obj, $query->getParamObject());
	}

	public function testCreateWhereLinesFromCity() {

		$query = $this->do2db->initializeCustomQuery();
		$values = array(
			'from_tables' => array('networks'),
			);

		$query->setValues($values);

		// create searchable
		$searchable = new \dobj\City();
		$searchable->id = 1;
		$searchable->name = 'Flipper';

		$lines = $query->createWhereLinesFromSearchable($searchable, 'networks', 'origin');

		foreach ($lines as $line) {
			$query->insertWhereLine($line);
		}

		$write_string = "SELECT * FROM networks WHERE id_city_origin=?";
		$this->assertEquals($write_string, $query->writeQueryString());
	}

	public function testCreateWhereLinesFromRegion() {

		$query = $this->do2db->initializeCustomQuery();
		$values = array(
			'from_tables' => array('networks'),
			);

		$query->setValues($values);

		// create searchable
		$searchable = new \dobj\Region();
		$searchable->id = 1;
		$searchable->name = 'Flipper';

		$lines = $query->createWhereLinesFromSearchable($searchable, 'networks', 'origin');

		foreach ($lines as $line) {
			$query->insertWhereLine($line);
		}

		$write_string = "SELECT * FROM networks WHERE id_region_origin=? AND id_city_origin IS NULL";
		$this->assertEquals($write_string, $query->writeQueryString());
	}

	public function testCreateWhereLinesFromCountry() {

		$query = $this->do2db->initializeCustomQuery();
		$values = array(
			'from_tables' => array('networks'),
			);

		$query->setValues($values);

		// create searchable
		$searchable = new \dobj\Country();
		$searchable->id = 1;
		$searchable->name = 'Flipper';

		$lines = $query->createWhereLinesFromSearchable($searchable, 'networks', 'origin');

		foreach ($lines as $line) {
			$query->insertWhereLine($line);
		}

		$write_string = "SELECT * FROM networks WHERE id_country_origin=? AND id_city_origin IS NULL AND id_region_origin IS NULL";
		$this->assertEquals($write_string, $query->writeQueryString());
	}

	public function testAddParenthetical() {

		$query = $this->do2db->initializeCustomQuery();
		$values = array(
			'from_tables' => array('networks'),
			);

		$query->setValues($values);

		// create searchable
		$searchable = new \dobj\Country();
		$searchable->id = 1;
		$searchable->name = 'Flipper';

		$lines = $query->createWhereLinesFromSearchable($searchable, 'networks', 'origin');

		$query->addAParenthetical($lines);

		$write_string = "SELECT * FROM networks WHERE (id_country_origin=? AND id_city_origin IS NULL AND id_region_origin IS NULL)";
		$this->assertEquals($write_string, $query->writeQueryString());
	}

	public function testAddFourParentheticals() {

		$query = $this->do2db->initializeCustomQuery();
		$values = array(
			'from_tables' => array('networks'),
			);

		$query->setValues($values);

		// create searchables
		$origin_searchable = new \dobj\Country();
		$origin_searchable->id = 1;
		$origin_searchable->name = 'Flipper';

		$location_searchable = new \dobj\Region();
		$location_searchable->id = 3321;
		$location_searchable->name = 'Oregon';

		$origin_lines = $query->createWhereLinesFromSearchable($origin_searchable, 'networks', 'origin');
		$location_lines = $query->createWhereLinesFromSearchable($location_searchable, 'networks', 'location', 'AND');

		$merged_lines = array_merge($origin_lines, $location_lines);

		$query->addAParenthetical($merged_lines);
		$query->addAParenthetical($merged_lines, 'OR');
		$query->addAParenthetical($merged_lines, 'OR');
		$query->addAParenthetical($merged_lines, 'OR');

		$write_string = "SELECT * FROM networks WHERE (id_country_origin=? AND id_city_origin IS NULL AND id_region_origin IS NULL AND id_region_cur=? AND id_city_origin IS NULL) OR (id_country_origin=? AND id_city_origin IS NULL AND id_region_origin IS NULL AND id_region_cur=? AND id_city_origin IS NULL) OR (id_country_origin=? AND id_city_origin IS NULL AND id_region_origin IS NULL AND id_region_cur=? AND id_city_origin IS NULL) OR (id_country_origin=? AND id_city_origin IS NULL AND id_region_origin IS NULL AND id_region_cur=? AND id_city_origin IS NULL)";
		$this->assertEquals($write_string, $query->writeQueryString());
		$this->assertTrue($query->columnCountMatchesValues());
		//var_dump($query->getParamObject());
		//var_dump($query->getWhereColumns());
	}

	public function testJoin() {

		$query = $this->do2db->initializeCustomQuery();
		$values = array(
			'select_rows' => array('n.*', 'mc.member_count', 'pc.post_count'),
			'from_tables' => array('networks n'),
			);

		$query->setValues($values);
		$query->addJoinStatementRaw('LEFT JOIN (SELECT id_network, COUNT(id_network) AS member_count FROM network_registration GROUP BY id_network ORDER BY member_count) mc ON mc.id_network=n.id');
		$query->addJoinStatementRaw('LEFT JOIN (SELECT id_network, COUNT(id_network) AS post_count FROM posts GROUP BY id_network ORDER BY post_count) pc ON pc.id_network=n.id AND pc.id_network=mc.id_network');

		$write_string = "SELECT n.*, mc.member_count, pc.post_count FROM networks n LEFT JOIN (SELECT id_network, COUNT(id_network) AS member_count FROM network_registration GROUP BY id_network ORDER BY member_count) mc ON mc.id_network=n.id LEFT JOIN (SELECT id_network, COUNT(id_network) AS post_count FROM posts GROUP BY id_network ORDER BY post_count) pc ON pc.id_network=n.id AND pc.id_network=mc.id_network";
		$this->assertEquals($write_string, $query->writeQueryString());
	}
}
