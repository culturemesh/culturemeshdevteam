<?php

final class Environment {
	
	// COMPILE TIME PROPERTIES //
	public static $site_root = __FILE__;

	private static $domain_url = 'http://www.culturemesh.com';
	private static $short_domain_url = 'culturemesh.com';
	private static $domain_name = 'CultureMesh';
	private static $facebook_url = '';
	private static $twitter_url = '';
	private static $support_email = '';
	
	// RUNTIME PROPERTIES //
	private $host_root;
	private $f_root;	// needed for AltoRouter
	private $ds;
	private $template_dir;
	private $env_file;
	private $img_dir;
	private $blank_img;
	private $g_api_key;

	private $db_server;
	private $db_user;
	private $db_pass;
	private $db_name;


	// BIGGUMS
	private static $environment = NULL;
	private static $connection = NULL;

	public function __construct() {

		// make environment the working directory
		chdir(dirname(__FILE__));
		self::$site_root = getcwd();

		$doc_root = $_SERVER['DOCUMENT_ROOT'];

		// returns hostname
		// eg - http://www.culturemesh.com/
		// eg - localhost
		// nothing if executing a script
		//
		if (isset($_SERVER['HTTP_HOST'])) {
			$hostname = $_SERVER['HTTP_HOST'];
			$this->host_root = 'http://'.str_replace($doc_root, $hostname, getcwd());
			$this->f_root = str_replace($doc_root, '', getcwd());

		}
		else {
			$this->host_root = 'unimportant';
		}


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
		$this->template_dir = self::$site_root.DIRECTORY_SEPARATOR.'templates';
		$this->ds = DIRECTORY_SEPARATOR;

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

			include "../../../abcd123.php";

			$this->db_name = $DB_NAME;
			$this->db_pass = $DB_PASS;

			// leave
			return True;
		}

		include  self::$site_root . DIRECTORY_SEPARATOR . "../localdbconn.php";
		//include "../../localdbconn.php";
		
		$this->db_name = $DB_NAME;
		$this->db_pass = $DB_PASS;
		$this->db_server = $DB_SERVER;
		$this->db_user = $DB_USER;

		$this->g_api_key = $G_API_KEY;

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

	public static function returnOldMysqli() {

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
		$class = str_replace("\\", DIRECTORY_SEPARATOR, $class);

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
			self::$connection = new PDO("mysql:host={$this->db_server};dbname={$this->db_name};", $this->db_user, $this->db_pass);
		}

		return self::$connection;
	}

	public function getMysqliConnection() {

		if (self::$connection == NULL) {
			self::$connection = new mysqli($this->db_server,$this->db_user,$this->db_pass,$this->db_name);
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

	public function getBaseTemplate() {
		return file_get_contents($this->template_dir . $this->ds . 'base.html');
	}

	public function getVars() {
		/*
	$site_root = __FILE__;

	  $domain_url = 'http://www.culturemesh.com';
	  $short_domain_url = 'culturemesh.com';
	  $domain_name = 'CultureMesh';
	  $facebook_url = '';
	  $twitter_url = '';
	  $support_email = '';
	
	// RUNTIME PROPERTIES //
	 $ds;
	 $template_dir;
	 $env_file;
	 $img_dir;
	 $blank_img;

	 $db_server;
	 $db_user;
	 $db_pass;
	 $db_name;
		 */
		return array(
			'home_path' => $this->host_root,
			'img_path' => $this->img_dir
		);
	}
}

?>
