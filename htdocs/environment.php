<?php
final class Environment {

	// COMPILE TIME PROPERTIES //

	/*
	 * The absolute file directory for the server
	 * - used for internal php access
	 */
	public static $site_root = __FILE__;
	public static $host_root_s;
	public static $hostname_s;
	private static $domain_url = 'http://www.culturemesh.com';
	private static $short_domain_url = 'culturemesh.com';
	private static $domain_name = 'CultureMesh';
	private static $facebook_url = '';
	private static $twitter_url = '';
	private static $support_email = '';

	// RUNTIME PROPERTIES //

	/*
	 * The file directory as it needs to be accessed
	 * on the website
	 *
	 * ex: www.culturemesh.build
	 */
	private $hostname;

	/*
	 * The file directory as it needs to be accessed
	 * in an html file
	 *
	 * ex: //www.culturemesh.build
	 */
	private $host_root;
	private $f_root; // needed for AltoRouter
	private $ds;
	private $template_dir;
	private $cache_dir;
	private $env_file;
	private $img_dir;
	private $img_repo_dir;
	private $img_host_repo;
	private $blank_img;
	private $g_api_key;
	private $db_server;
	private $db_user;
	private $db_pass;
	private $db_name;
	private $session_name;

	private $maintenance_info;

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
			$this->hostname = $_SERVER['HTTP_HOST'];
			if (strpos($_SERVER['REQUEST_URI'], 'culturemeshdevteam') !== False)
				$this->hostname .= '/culturemeshdevteam/htdocs';

			$hostname = $this->hostname;
			$this->host_root = '//'.str_replace($doc_root, $hostname, getcwd());
			$this->f_root = str_replace($doc_root, '', getcwd());
			$this->img_host_repo = $this->host_root.'/../../user_images';

			// doing this until I can test to make sure I can get rid of host_root
			if (strpos($_SERVER['REQUEST_URI'], 'culturemeshdevteam') !== False)
				$this->img_host_repo = '//'.$_SERVER['HTTP_HOST'].'/../../user_images';
		}
		else {
			$this->host_root = 'unimportant';
		}
		if( !$this::includeEnvFiles() ) {
			throw new Exception('Could not find environment files');
		}

		// setup autoload
		$this::setupAutoload();
		$this->img_dir;
		// $this->img_repo_dir = self::$site_root.DIRECTORY_SEPARATOR.'..'. DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'user_images';
		$this->img_repo_dir = '/home3/culturp7/public_html/user_images';
		$this->blank_img;
		$this->template_dir = self::$site_root.DIRECTORY_SEPARATOR.'templates';
		$this->cache_dir = self::$site_root.DIRECTORY_SEPARATOR.'cache';
		$this->ds = DIRECTORY_SEPARATOR;
		self::$environment = $this;

		// setup maintenance stuff
		$this->maintenance_info = array(
			'notification' => $GLOBALS['MAINTENANCE_NOTIFICATION'],
			'date' => $GLOBALS['MAINTENANCE_DATE'],
			'duration' => $GLOBALS['MAINTENANCE_DURATION']
		);
	}

	public function __get($name) {
		if (isset($this->$name))
			return $this->$name;
		else
			return false;
	}

	public static function site_root() {
		return getcwd();
	}

	public static function hostname() {
		if (!isset(self::$hostname_s))
			self::$hostname_s = $_SERVER['HTTP_HOST'];
		// return
		return self::$hostname_s;
	}

	public static function host_root() {
		if (!isset(self::$host_root_s)) {
			$doc_root = $_SERVER['DOCUMENT_ROOT'];
			$hostname = $_SERVER['HTTP_HOST'];
			self::$host_root_s = '//'.str_replace($doc_root, $hostname, getcwd());
		}
		return self::$host_root_s;
	}

	private function includeEnvFiles() {
		if (!file_exists('../localdbconn.php')
			&& !file_exists('../../localdbconn.php'))
		{
			$this->db_user = "culturp7";
			// other shiz
			if ( !file_exists("../../../abcd123.php")
				&& !file_exists("../../../../abcd123.php"))
			{
				return false;
			}
			if ( file_exists("../../abcd123.php"))
				include "../../abcd123.php";
			if ( file_exists("../../../abcd123.php"))
				include "../../../abcd123.php";
			if ( file_exists("../../../../abcd123.php"))
				include "../../../../abcd123.php";
			$this->db_server = $DB_SERVER;
			$this->db_name = $DB_NAME;
			$this->db_pass = $DB_PASS;
			$this->g_api_key = $GLOBALS['G_API_KEY'];
			$this->session_name = $SESSION_NAME;
			// leave
			return True;
		}
		include self::$site_root . DIRECTORY_SEPARATOR . "../localdbconn.php";
		//include "../../localdbconn.php";
		$this->db_name = $DB_NAME;
		$this->db_pass = $DB_PASS;
		$this->db_server = $DB_SERVER;
		$this->db_user = $DB_USER;
		$this->g_api_key = $G_API_KEY;
		return true;
	}

	public static function returnOldMysqli() {
	}

	private function setupAutoload() {

		// get composer autoload
		include 'vendor/autoload.php';
		require_once('vendor/phpfastcache/phpfastcache/phpfastcache.php');

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
		$file = Environment::$site_root."/tests/"."{$class}.php";
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

	public function enableDatabase(&$dal, &$do2db) {

		$dal = new \dal\DAL($this->getConnection());
		$dal->loadFiles();
		$do2db = new \dal\Do2db();
	}

	public static function tearDown() {
		self::$environment = NULL;
	}

	public function getBaseTemplate() {
		return file_get_contents($this->template_dir . $this->ds . 'base.html');
	}

	public function getDefaultProfileImage() {
		return $this->host_root . 'images/blank_profile.png';
	}

	/*
	 * gets a bunch of relevant variables
	 * Fo' mustache
	 */
	public function getVars() {
		return array(
			'img_host_repo' => $this->img_host_repo,
			'home_path' => $this->host_root,
			'f_root' => $this->f_root,
			'img_path' => $this->img_dir,
			'hostname' => '//' . $this->hostname,
			'maintenance' => $this->maintenance_info,
			'l_brace' => '{{',
			'r_brace' => '}}'
		);
	}
	
	public function displayErrors() {
		
		// ----------------------------------------------------------------------------------------------------
		// - Display Errors
		// ----------------------------------------------------------------------------------------------------
		ini_set('display_errors', 'On');
		ini_set('html_errors', 0);
		
		// ----------------------------------------------------------------------------------------------------
		// - Error Reporting
		// ----------------------------------------------------------------------------------------------------
		error_reporting(-1);
		
		// ----------------------------------------------------------------------------------------------------
		// - Shutdown Handler
		// ----------------------------------------------------------------------------------------------------
		function ShutdownHandler()
		{
		    if(@is_array($error = @error_get_last()))
		    {
		        return(@call_user_func_array('ErrorHandler', $error));
		    };
		
		    return(TRUE);
		};
		
		register_shutdown_function('ShutdownHandler');
		
		// ----------------------------------------------------------------------------------------------------
		// - Error Handler
		// ----------------------------------------------------------------------------------------------------
		function ErrorHandler($type, $message, $file, $line)
		{
		    $_ERRORS = Array(
		        0x0001 => 'E_ERROR',
		        0x0002 => 'E_WARNING',
		        0x0004 => 'E_PARSE',
		        0x0008 => 'E_NOTICE',
		        0x0010 => 'E_CORE_ERROR',
		        0x0020 => 'E_CORE_WARNING',
		        0x0040 => 'E_COMPILE_ERROR',
		        0x0080 => 'E_COMPILE_WARNING',
		        0x0100 => 'E_USER_ERROR',
		        0x0200 => 'E_USER_WARNING',
		        0x0400 => 'E_USER_NOTICE',
		        0x0800 => 'E_STRICT',
		        0x1000 => 'E_RECOVERABLE_ERROR',
		        0x2000 => 'E_DEPRECATED',
		        0x4000 => 'E_USER_DEPRECATED'
		    );
		
		    if(!@is_string($name = @array_search($type, @array_flip($_ERRORS))))
		    {
		        $name = 'E_UNKNOWN';
		    };
		
		    return(print(@sprintf("%s Error in file \xBB%s\xAB at line %d: %s\n", $name, @basename($file), $line, $message)));
		};

		$old_error_handler = set_error_handler("ErrorHandler");
	}
}
?>
