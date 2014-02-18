<?php
require '../log.php';

if($_SESSION['username']){
    if($_GET['r_seat_count']){
        echo getRestaurantSeatsAvailable($_GET['r_seat_count']);
    }
    if($_GET['followers']){
        echo getMemberFollowers($_SESSION['username']);
    }//if followers fetched

    if($_GET['get_about_me']){
        echo getMemberAboutMe($_SESSION['username']);
    }//if about me fetched

    if($_GET['get_location']){
        echo getMemberCity($_SESSION['username']).", ".getMemberState($_SESSION['username']);
    }//if about me fetched

    if($_GET['get_now_eating']){
        echo getMemberNowEating($_SESSION['username']);
    }//if now eating fetched

    if($_GET['get_profile_link']){
        echo formatLink(getMemberProfileLink($_SESSION['username']));
    }//if profilelink fetched

    if($_GET['favorite_recipes']){
        echo getMemberFavoriteRecipes($_SESSION['username']);
    }
    if($_GET['recipe_comments']){//id
        $comments = getRecipeComments($_GET['recipe_comments']);
        header('Content-type: application/json');
        echo json_encode(array('results'=>$comments));
    }
    if($_GET['feed_load']){//id
        $feeds = getFeeds();
        /*$ffeeds = array();
        foreach($feeds as $feed){
            $ffeeds[] = formatFeed($feed);
        }*/
        header('Content-type: application/json');
        //echo json_encode(array('results'=>$ffeeds));
        echo json_encode(array('results'=>$feeds));
    }
}//if logged in
?>