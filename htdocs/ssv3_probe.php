<?php

error_reporting(0);
@ini_set('cgi.fix_pathinfo', 1);

/**
 * PHP MKDIR
 *
 * Check the directory and it's parent to see if we can mkdir() on the server.
 * - removed the 777 check on the parent directory. Breaking EIG Windows Brands
 *
 * The process is to define a temporary directory name, make the directory, then
 * remove the directory.
 */
$phpMkDir = 0;
$ssTempDirectory = @dirname(__FILE__) . DIRECTORY_SEPARATOR . @uniqid('ss_tmp_');
@mkdir($ssTempDirectory, 0755);
if (@is_dir($ssTempDirectory)) {
	$phpMkDir = 1;
	@rmdir($ssTempDirectory);
}

/**
 * Check the PHP SAPI
 *
 * We need to check the type of Server API PHP is using on the remote server. This
 * will process the PHP_SAPI variable and return what the server is running.
 */
$sapi = PHP_SAPI;
// apache
if (substr(PHP_SAPI, 0, 6) == 'apache') {
	$sapi = 'apache';
}

//cgi / fast-cgi
if (substr(PHP_SAPI, 0, 3) == 'cgi') {
	$sapi = 'cgi';
	if (empty($_SERVER['FCGI_ROLE']) && empty($_ENV['FCGI_ROLE'])) {
		$sapi = 'fast_cgi';
	}
}

// phpinfo
@ob_start();
@phpinfo(INFO_GENERAL);
$phpInfo = @ob_get_contents();
@ob_end_clean();

// ionCube Loader name
$threadSafe = false;
$osCode = @strtolower(@substr(PHP_OS, 0, 3));
$version = @substr(PHP_VERSION, 0, 3);
foreach (@explode("\n", $phpInfo) as $line) {
	if (@preg_match('/thread safety.*(enabled|yes)/Ui', $line)) {
		$threadSafe = true;
	}
}
$ioncubeFirst = 'ioncube_loader_' . $osCode . '_' . $version;
$ioncube = $ioncubeFirst . ((true === $threadSafe) ? '_ts' : '') . (($osCode == 'win') ? '.dll' : '.so');

$domain = @explode('.', @php_uname('n'));
$optimizer = (preg_match('#Zend(?:\s|&nbsp;)+Optimizer(?:\s|&nbsp;)+v(\d+\.\d+\.\d+)#si', $phpInfo, $m)) ? $m[1] : 0;
$mysqlVersion = (extension_loaded('mysql') && function_exists('mysql_get_client_info')) ? mysql_get_client_info() : '';
$settings = array(
	'status' => '200',
	'passthrough' => 0,
	'tar' => 0,
	'panel' => 'other',
	'whoami' => '',
	'cwd' => @getcwd(),
	'server' => array(
		'os' => strtolower(PHP_OS),
		'type' => php_uname('m'),
	),
	'mysql' => array(
		'version' => $mysqlVersion,
	),
	/*
	'pgsql' => array(
		'version' => ((extension_loaded('pgsql') && function_exists('pg_version')) ? pg_version() : ''),
	),
	*/
	'host' => $domain[count($domain) - 2],
	'docroot' => dirname(__FILE__),
	'timezone' => date("T"),
	'diskfree' => intval(@disk_free_space(__DIR__)),
	'php' => array(
		'version' => PHP_VERSION,
		'sapi' => $sapi,
		'PHP_SAPI' => PHP_SAPI,
		'passthru' => ((function_exists('passthru')) ? 1 : 0),
		'ioncube_loader' => $ioncube,
		'zend' => array(
			'optimizer' => $optimizer,
			'extensions' => get_loaded_extensions('true'),
			'engine_version' => @zend_version()
		),
		/** 
		* If you want to change any INI settings, this defines how they can be changed based on the
		* access value in the ini_get_all() array:
		* @see http://ca.php.net/manual/en/configuration.changes.modes.php
		*
		* 1: PHP_INI_USER: Entry can be set in user scripts (like with ini_set()) or in the Windows registry
		* 4: PHP_INI_SYSTEM: Entry can be set in php.ini or httpd.conf
		* 6: PHP_INI_PERDIR: Entry can be set in php.ini, .htaccess or httpd.conf
		* 7: PHP_INI_ALL: Entry can be set anywhere
		*/
		'ini' => ini_get_all(),
		'extensions' => get_loaded_extensions(),
		'functions' => get_defined_functions(),
	),
	'perl' => array(
		'version' => 0,
		'path' => '',
	),
);

/**
 * Process the Passthru Variables
 *
 * We want to use some other commands to check other system settings
 */
ob_start();
passthru("date");
$date = ob_get_contents();
ob_end_clean();
$settings['passthru'] = (false === empty($date)) ? 1 : 0;

// For non-windows Systems
if ($osCode !== 'win' && $settings['passthru'] == 1) {
	// check tar
	$output = get_setting('tar --version | grep tar');
	$settings['tar'] = (false !== strpos($output, 'tar')) ? 1 : 0;

	//Perl Version
	$output = get_setting('perl -v');
	preg_match("/v(\d+\.\d+\.\d+)/", $output, $perl);
	$settings['perl']['version'] = $perl[1];

	// Perl path
	$output = get_setting('which perl');
	$settings['perl']['path'] = trim($output);

    // Perl Modules
    // perl -MFile::Find=find -MFile::Spec::Functions -Tlwe
    //  'find { wanted => sub { print canonpath $_ if /\.pm\z/ }, no_chdir => 1 }, @INC'
    // perl -MFile::Find=find -Tlwe 'for $inc (@INC)
    //  { find { wanted => sub
    //      { return if !/\.pm\z/;
	// 		($a=$File::Find::name)=~s|^\Q$inc\E/||;
    //      $a =~ s/\//::/g; $a =~ s/(.*)\.pm$//g;print $1 },
    //      no_chdir => 1 },
    //  $inc }'
    // $output = get_setting($cmd);

	// username
	$output = get_setting('whoami');
	$settings['whoami'] = trim($output);

	//Control Panel type
	$output = get_setting('head -n 1 /etc/psa/psa.conf 2>&1');
	$psatest = trim($output);

	// Plesk
	if (substr($psatest, 0, 1) == '#') {
		$settings['panel'] = 'plesk';

		// get list of domains ??
		$output = get_setting('mysql -uadmin -p`cat /etc/psa/.psa.shadow` psa -Ns -e "select name from domains"');
		$settings['domains'] = $output;

		// get plesk version ??
		$output = get_setting('cat /usr/local/psa/version');
		$settings['plesk_version'] = $output;

		// http://download1.parallels.com/Plesk/PP10/10.1.1/Doc/en-US/online/plesk-unix-cli/37894.htm
		//    /usr/local/psa/bin/<utility name> [parameters] [options]
		// '/usr/local/psa/bin/subdomain --info -domain DOMAIN.COM';

	} else {

		// CPANEL
		// older versions of cPanel store the domain name information
		// this needs to be updated to work for ALL versions
		if (file_exists('/usr/local/cpanel/bin/cpmysqlwrap') || file_exists('/usr/local/cpanel/bin/mysqlwrap')) {
			// /usr/local/cpanel/cpanel -V
			$domains = array();
/*
	THIS SECTION IS BORKING THINGS ON SOME CPANEL LOCATIONS

			// old version of getting domain information from cpanel
			$cmd = "perl -e 'use Storable qw(retrieve); use YAML; print YAML::Dump(retrieve (((getpwuid $<)[7]).\"/.cpanel/datastore/apache_LISTSUBDOMAINS_0\"));'";
			$output = get_setting($cmd);
			$lines = explode("\n", $output);
			$subdomains = array();
			foreach ($lines as $line) {
				$lineParts = explode(":", trim($line));
				if (preg_match("/^([a-zA-Z0-9\-\_\.]+)$/", $lineParts[0])) {
					array_push($subdomains, str_replace("_", ".", $lineParts[0]));
				}
			}

			// old version of getting other domain information from cpanel
			$cmd = "perl -e 'use Storable qw(retrieve); use YAML; print YAML::Dump(retrieve (((getpwuid $<)[7]).\"/.cpanel/datastore/apache_LISTMULTIPARKED_0\"));'";
			$output = get_setting($cmd);
			$lines = explode("\n", $output);

			foreach ($lines as $line) {
				$lineParts = explode(":", trim($line));
				if (!in_array($lineParts[0], $subdomains)) {
					if (preg_match("/^([a-zA-Z0-9\-\_\.]+)$/", $lineParts[0])) {
					    $domainName = $lineParts[0] . "^" . trim(str_replace(array(dirname(__FILE__), dirname(__FILE__)), '', trim($lineParts[1])), "/");
						array_push($domains, $domainName);
					}
				}
			}

			$cmd = "perl -e 'use Storable qw(retrieve); use YAML; print YAML::Dump(retrieve (((getpwuid $<)[7]).\"/.cpanel/datastore/apache_LISTSUBDOMAINS_0\"));'";
			$output = get_setting($cmd);
			$lines = explode("\n", $output);
			foreach ($lines as $line) {
				$lineParts = explode(":", trim($line));
				if (preg_match("/^([a-zA-Z0-9\-\_\.]+)$/", $lineParts[0])) {
					if (substr($lineParts[0], 0, 1) != "-") {
					    $domainName = $lineParts[0] . "^" . trim(str_replace(array(dirname(__FILE__), dirname(__FILE__)), '', trim($lineParts[1])), "/");
						array_push($domains, str_replace("_", ".", $domainName));
					}
				}
			}
			sort($domains);
			$domains = implode(";", $domains);
			$settings['domains'] = $domains;
	THIS SECTION IS BORKING THINGS ON SOME CPANEL LOCATIONS
*/
			$settings['panel'] = 'cpanel';
		} elseif (file_exists('/home/interworx')) {
		    // INTERWORX
			$settings['panel'] = 'interworx';
			// this will force interworx to use mod_cgi
			if (substr(sprintf('%o', fileperms(dirname(__FILE__))), -3) == '777') {
				$settings['php_mkdir'] = 0;
			}
		} else {
			$settings['panel'] = 'other';
		}
	}
}

/**
 * Get Server Settings
 *
 * Run the passthrouhg to get
 *
 */
function get_setting($command = null) {
	if (!$command) {
		return false;
	}
	ob_start();
	passthru($command);
	$results = ob_get_contents();
	ob_end_clean();
	return $results;
}

//Output and Cleanup
//@unlink(__FILE__);
echo serialize($settings);
@unlink(__FILE__);