<?php

final class Environment {
	
	// COMPILE TIME PROPERTIES //
	public static $site_root = __DIR__;

	private static $domain_url = 'http://www.culturemesh.com';
	private static $short_domain_url = 'culturemesh.com';
	private static $domain_name = 'CultureMesh';
	private static $facebook_url = '';
	private static $twitter_url = '';
	private static $support_email = '';
	
	// RUNTIME PROPERTIES //
	private $template_dir;
	private $env_file;
	private $img_dir;
	private $blank_img;

	private $db_server;
	private $db_user;
	private $db_pass;
	private $db_name;

	// BIGGUMS
	private static $environment = NULL;
	private static $connection = NULL;

	public function __construct() {

		if( !$this::includeEnvFiles() ) {
			throw new Exception('Could not find environment files');
		}

		// setup autoload
		$this::setupAutoload();


		/*
		if (file_exists('../localdbconn.php')) {
			var_dump('THING EXISTS');
		}
		 */

		/*
		var_dump($DB_NAME);
		var_dump($DB_PASS);
		var_dump($DB_SERVER);
		var_dump($DB_USER);
		 */


		$this->img_dir;
		$this->blank_img;
		$this->template_dir = Environment::$site_root.DIRECTORY_SEPARATOR.'templates';

		self::$environment = $this;
	}

	public function __get($name) {

		if (isset($this->$name)) 
			return $this->$name;
		else
			return false;
	}

	public function testMode() {

		echo 'Decided I may not need this';
	}

	private function includeEnvFiles() {
		if (!file_exists('../localdbconn.php')
			&& !file_exists('../../localdbconn.php'))
		{
			$this->db_server = "localhost";
			$this->db_user = "culturp7";

		    	// other shiz
			if ( !file_exists("../../../abcd123.php") 
				&& !file_exists("../../../../abcd123.php"))
			{
				return false;
			}

			$this->db_name = $DB_NAME;
			$this->db_pass = $DB_PASS;

			// leave
			return True;
		}

		include "../localdbconn.php";
		include "../../localdbconn.php";
		
		var_dump($DB_NAME);


		$this->db_name = $DB_NAME;
		$this->db_pass = $DB_PASS;
		$this->db_server = $DB_SERVER;
		$this->db_user = $DB_USER;

		return true;

		/*
		if ( file_exists('../localdbconn.php'))
		{
		    include  "../localdbconn.php";

//		global $DB_NAME, $DB_PASS, $DB_SERVER, $DB_USER;
		    var_dump($DB_NAME);
		    return true;
		}
		else if ( file_exists('../../localdbconn.php'))
		{
		    include  "../../localdbconn.php";
		    return true;
		}
		else
		{
		    $this->db_server = "localhost";
		    $this->db_user = "culturp7";

		    if ( file_exists("../../../abcd123.php")) {
			include "../../../abcd123.php";
			return true;
		    }
		    else if ( file_exists("../../../../abcd123.php")) {
			include "../../../../abcd123.php";
			return true;
		    }
		}

		// if none of the files are there
		return false;
		 */
	}

	private function setupAutoload() {

		// get composer autoload
		include 'vendor/autoload.php';

		// set up autoloads
		spl_autoload_register('Environment::autoloadLib');
		spl_autoload_register('Environment::autoloadHT');
		spl_autoload_register('Environment::autoloadTest');
	}

	public static function autoloadHT($class)
	{
		$file = Environment::$site_root."/{$class}.php";

		if (file_exists($file)) {
			include $file;
		}
	}

	public static function autoloadLib($class)
	{
		$class = str_replace("\\", "/", $class);

		//$file = $_SERVER['DOCUMENT_ROOT'] . "/lib/{$class}.php";
		$file = Environment::$site_root.'/lib/'."{$class}.php";
		if (file_exists($file)) {
			include $file;
		}
	}

	public static function autoloadTest($class)
	{
		$class = str_replace("\\", "/", $class);

		$file =  Environment::$site_root."/tests/"."{$class}.php";

		if (file_exists($file)) {
			include $file;
		}
	}

	public static function getEnvironment() {

		if (self::$environment == NULL) {
			throw new Exception('Environment not initialized');
		}

		return self::$environment;
	}

	public function getConnection() {

		if (self::$connection == NULL) {
			//self::$connection = new mysqli($this->db_server,$this->db_user,$this->db_pass,$this->db_name);
			//
			self::$connection = new PDO("mysql: host={$this->db_server};dbname={$this->db_name};charset=utf-8", $this->db_user, $this->db_pass);
		}

		return self::$connection;
	}

	public static function getStaticConnection() {

		if (self::$environment == NULL) {
			throw new Exception('Environment has not been initialized');
		}

		// initializes connection,
		// activates self::connection
		if (self::$connection == NULL) {
			return self::$environment->getConnection();
		}

		return self::$connection;
	}

	public static function closeConnection() {
		self::$connection = NULL;
	}

	public static function tearDown() {

		self::$environment = NULL;
	}
}

?>
