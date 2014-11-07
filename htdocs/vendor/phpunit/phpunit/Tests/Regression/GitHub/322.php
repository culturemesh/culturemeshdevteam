<?php

$_SERVER['argv'][1] = '--configuration';
$_SERVER['argv'][2] = dirname(__FILE__).'/322/phpunit322.xml';
$_SERVER['argv'][3] = '--debug';
$_SERVER['argv'][4] = '--group';
$_SERVER['argv'][5] = 'one';
$_SERVER['argv'][6] = 'Issue322Test';
$_SERVER['argv'][7] = dirname(__FILE__).'/322/Issue322Test.php';

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/PHPUnit/Autoload.php';
PHPUnit_TextUI_Command::main();
?>
