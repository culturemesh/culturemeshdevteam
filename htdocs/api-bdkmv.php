<?php
ini_set('display_errors', 'On');

include('environment.php');
$cm = new \Environment();

echo '<br>';
echo 'API KEY: ';
echo $cm->g_api_key;

echo '<br>';
echo 'FILE APIKEY: ';
echo $GLOBALS['G_API_KEY'];


echo '<br><br><br>';
echo 'IMAGE REPO DIR: ';
echo $cm->img_repo_dir;


echo '<br><br><br>';
echo 'HOSTNAME: ';
echo $cm->hostname;


echo '<br><br><br><hr>';
echo 'SERVERVARS';
echo '<hr>';

echo '<br><br>';
echo 'HTTP_HOST: ';
echo $_SERVER['HTTP_HOST'];


echo '<br><br><br>';
echo 'DOCUMENT ROOT: ';
echo $_SERVER['DOCUMENT_ROOT'];
?>
