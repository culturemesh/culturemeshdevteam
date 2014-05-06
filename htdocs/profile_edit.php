<?php
    ini_set('display_errors', true);
    error_reporting(E_ALL ^ E_NOTICE);
    session_name("myDiaspora");
    session_start();
    
    include_once("data/dal_user.php");
    // exit if there's no uid, will probably change to something else
    if ( !isset($_SESSION['uid'])) exit ('Location: index.php?logout=true');
    
    $user_email = User::getMemberEmail($_SESSION['uid']);
    //$ppre = getCurrentFilename(__FILE__);
    $ppre = 'profile_edit_pg';
    include 'page_tpl.php';
    include_once 'zz341/fxn.php';
?>
