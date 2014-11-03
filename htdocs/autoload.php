<?php

// get composer autoload
include 'vendor/autoload.php';

class Autoload {
	
	public static function autoloadHT($class)
	{
		$file = __DIR__."{$class}.php";

		if (file_exists($file)) {
			include $file;
		}
	}

	public static function autoloadLib($class)
	{
		$class = str_replace("\\", "/", $class);

		//$file = $_SERVER['DOCUMENT_ROOT'] . "/lib/{$class}.php";
		$file = __DIR__.'/lib/'."{$class}.php";
		if (file_exists($file)) {
			include $file;
		}
	}

	public static function autoloadTest($class)
	{
		$file =  __DIR__."/tests/{$class}.php";

		if (file_exists($file)) {
			include $file;
		}
	}

	private function slashConvert($class) {

		return str_replace("\\", "/", $class);
	}
}

spl_autoload_register('Autoload::autoloadLib');
spl_autoload_register('Autoload::autoloadHT');
spl_autoload_register('Autoload::autoloadTest');
?>
