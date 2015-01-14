<?php
namespace db;
//require('Environment.php');

abstract class DatabaseTest extends \PHPUnit_Extensions_Database_TestCase {

	// only instantiate pdo once for test clean-up/fixture load
	//static private $pdo = null;
	protected static $dbh;

	protected $self_fixtures = array();

	// only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
	private $conn = null;

	/*
	public function getConnection()
	{
		if ($this->conn === null) {
			if (self::$pdo == null) {
				self::$pdo = new PDO('sqlite::memory:');
			}
			$this->conn = $this->createDefaultDBConnection(self::$pdo, ':memory:');
		}

		return $this->conn;
	}
	 */
	public static function setUpBeforeClass()
	{
		self::$dbh = new \PDO('sqlite::memory:');
	}

	public static function tearDownAfterClass()
	{
		self::$dbh = NULL;
	}

	public function getConnection() {

		if ($this->conn === null) {
			try {
				//$pdo = new PDO('sqlite::memory');
				//$this->conn = $this->createDefaultDBConnection($pdo, ':memory:');
				$this->conn = $this->createDefaultDBConnection(self::$dbh, ':memory:');
				return $this->conn;
			} 
			catch (PDOException $e) {

				echo $e->getMessage();
			}
		}

		return $this->conn;

	}

	public function getDataSet($fixtures=array()) {

		/*
		if (empty($fixtures)) {
			$fixtures = $this->fixtures;
		}

		$compositeDs = new
			\PHPUnit_Extensions_Database_DataSet_CompositeDataSet(array());

		$fixturePath = \Environment::$site_root.'/tests/fixtures';

		foreach ($fixtures as $fixture) {
			
			$ext = DIRECTORY_SEPARATOR . $fixture;

			// hackish and lame, but for now...
			if ($files != NULL) {
			foreach ($files[$fixture] as $file) {

				$path =  $fixturePath . $ext . DIRECTORY_SEPARATOR . "cmdata-$file.xml";
				$ds = $this->createMySQLXMLDataSet($path);
				$compositeDs->addDataSet($ds);
			}
			}
			else 
			{
				$path =  $fixturePath . $ext . DIRECTORY_SEPARATOR . "cmdata-$fixture.xml";
				$ds = $this->createMySQLXMLDataSet($path);
				$compositeDs->addDataSet($ds);
			}
		}
		 */

		if (empty($fixtures)) {
			$fixtures = $this->self_fixtures;
		}
		
		$compositeDs = new
		\PHPUnit_Extensions_Database_DataSet_CompositeDataSet(array());
	
		if (!empty($fixtures)) {
			$files = $fixtures->getFilenames();
			
			$fixturePath = \Environment::$site_root.'/tests/fixtures';

			// loop through files, create dataset, add
			foreach($files as $file) {

				$path =  $fixturePath . DIRECTORY_SEPARATOR . $file;
				$ds = $this->createMySQLXMLDataSet($path);
				$compositeDs->addDataSet($ds);
			}
		}

		return $compositeDs;
	}


	public function loadDataSet($dataSet) {

		// set the new dataset
		$this->getDatabaseTester()->setDataSet($dataSet);
		// call setUp which adds the rows
		$this->getDatabaseTester()->onSetUp();
	}


	public function setUp() {

		//$conn = $this->getConnection();
		//$pdo = $conn->getConnection();

		// set up tables
		$fixtureDataSet = $this->getDataSet($this->self_fixtures);

		foreach ($fixtureDataSet->getTableNames() as $table) {

			// drop table
			//$pdo->exec("DROP TABLE IF EXISTS `$table`;");
			self::$dbh->exec("DROP TABLE IF EXISTS `$table`;");

			// recreate table
			$meta = $fixtureDataSet->getTableMetaData($table);
			$create = "CREATE TABLE IF NOT EXISTS `$table` ";
			$cols = array();
			foreach ($meta->getColumns() as $col) {
				$cols[] = "`$col` VARCHAR(200)";
			}
			$create .= '('.implode(',', $cols).');';
			self::$dbh->exec($create);
			//$pdo->exec($create);
		}

		parent::setUp();
	}

	public function tearDown() {

		$allTables =
			$this->getDataSet($this->self_fixtures)->getTableNames();
		foreach ($allTables as $table) {
			// drop table
			/*
			$conn = $this->getConnection();
			$pdo = $conn->getConnection();
			$pdo->exec("DROP TABLE IF EXISTS `$table`;");
			 */
			self::$dbh->exec("DROP TABLE IF EXISTS `$table`;");
		}

		parent::tearDown();
	}

	/*
	public function testDataBaseConnection() {

		$this->getConnection()->createDataSet(array('users'));
		$prod = $this->getDataSet();
		$queryTable = $this->getConnection()->createQueryTable(
			'users', 'SELECT * FROM users'
		);

		$expectedTable = $this->getDataSet()->getTable('users');

		// check data in xml file
		$this->assertTablesEqual($expectedTable, $queryTable);
	}

	public function testDataBaseConnection2() {

		$this->getConnection()->createDataSet(array('networks'));
		$prod = $this->getDataSet();
		$queryTable = $this->getConnection()->createQueryTable(
			'networks', 'SELECT * FROM networks'
		);
		$expectedTable = $this->getDataSet()->getTable('networks');

		// check data in xml file
		$this->assertTablesEqual($expectedTable, $queryTable);
	}
	 */
}
?>
