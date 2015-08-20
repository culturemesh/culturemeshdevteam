<?php
    ini_set('display_errors', true);
    error_reporting(E_ALL ^ E_NOTICE);
	include 'environment.php';
	$cm = new Environment();
    session_name($cm->session_name);
    session_start();
    
    include_once("data/dal_user.php");
    // exit if there's no uid, will probably change to something else
    if ( !isset($_SESSION['uid'])) header ('Location: index.php?signout=You have been signed out.');
    
    $user_email = User::getMemberEmail($_SESSION['uid']);
    //$ppre = getCurrentFilename(__FILE__);
    $ppre = 'profile_edit_pg';
    include 'page_tpl.php';
    include_once 'zz341/fxn.php';
?>
