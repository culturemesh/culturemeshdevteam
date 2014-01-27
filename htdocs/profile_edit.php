<?php
    ini_set('display_errors', true);
    error_reporting(E_ALL ^ E_NOTICE);
    session_name("myDiaspora");
    session_start();
    
    // exit if there's no uid, will probably change to something else
    if ( !isset($_SESSION['uid'])) exit ('you are not logged in </br><a href="index.php">Back to Home</a>');
    
    //$ppre = getCurrentFilename(__FILE__);
    $ppre = 'profile_edit_pg';
    include 'page_tpl.php';
    include_once 'html_builder.php';
    include_once 'data/dal_event.php';
    include_once 'data/dal_event_registration.php';
    include_once 'data/dal_network.php';
    include_once 'data/dal_network_registration.php';
    include_once 'data/dal_post.php';
?>