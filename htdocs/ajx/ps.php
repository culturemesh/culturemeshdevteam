<?php
require '../log.php';

if($_POST['reg_email']){
    echo getIsEmailAvailable($_POST['reg_email']);
}//checking registration email
    
if($_SESSION['username']){
    if($_POST['update_settings']){
        mapFormDataToDB($_POST['update_settings']);
    }
    if($_POST['msg_body'] && $_POST['msg_to']){
        $subject = ($_POST['msg_subject']) ? $_POST['msg_subject'] : "NULL";
        echo sendMessage($subject, $_POST['msg_body'], getMemberUID($_SESSION['username']), getMemberUID($_POST['msg_to']));
    }
    if($_POST['update_restaurant_services']){
        $r_id = getMemberRestaurantID($_SESSION['username']);
        updateRestaurantServices($_POST['update_restaurant_services'], $r_id);
    }
    if($_POST['delete_gallery_photo']){
        if(file_exists($_SERVER['DOCUMENT_ROOT']."/users/".$_SESSION['username']."/gallery/".$_POST['delete_gallery_photo'])){
            unlink($_SERVER['DOCUMENT_ROOT']."/users/".$_SESSION['username']."/gallery/".$_POST['delete_gallery_photo']);
            if(!file_exists($_SERVER['DOCUMENT_ROOT']."/users/".$_SESSION['username']."/gallery/".$_POST['delete_gallery_photo'])){
                echo "1";
            }
            else{
                echo "2";
            }
        }
    }
    if($_POST['update_r_hours']){
        $r_id = getMemberRestaurantID($_SESSION['username']);
        echo updateRestaurantHours($_POST['update_r_hours'], $r_id);
        //print_r($_POST['update_r_hours']);
            
        //echo $_POST['update_r_hours'][$_POST['thursday_open']];
        /*foreach($_POST['update_r_hours'] as $ind=>$spec){
            if($spec['name'] == "wednesday_open"){
                echo $spec['value'];
            }
        }*/
    }
    if($_POST['cancel_reservation']){
        echo cancelReservation($_SESSION['username'], $_POST['cancel_reservation']);
    }
    if($_POST['reserve_seat'] && $_POST['reserved_for']){
        echo reserveSeat($_SESSION['username'], $_POST['reserved_for']);
    }
    if($_POST['discover_search'] && $_POST['query']){
        echo '<li>';
        switch($_POST['discover_search']){
            case "via_location":
                echo 'Dishes in '.$_POST['query'];
                if(!in_array($_POST['query'], $GLOBALS['states_assoc_array'])){
                    exit('Unknown location. Please try again.');
                }
                $state_abbr = array_search($_POST['query'], $GLOBALS['states_assoc_array']);
                $loc_users = getAllRegularMembersInState($state_abbr);
                $d_counter = 0;
                foreach($loc_users as $state_user){
                    $name = $state_user['name'];
                    $user = $state_user['username'];
                    $dishes = getMemberRecipes($user);
                    foreach($dishes as $dish){
                        $d_counter +=1;
                        $d_name = $dish['name'];
                        $d_id = $dish['id'];
                        $d_flags = getRecipeFlags($d_id);
                        $d_thumb = (count($d_flags)>=5) ? "default_recipe_thumb.png" : ( (strlen($dish['thumb_url'])>0) ? $dish['thumb_url'] : "default_recipe_thumb.png");
                        echo formatDishSearchResult($dish);
                        include $_SERVER['DOCUMENT_ROOT']."/recipe-gallery-modal.php";
                        if($d_counter%4 == 0){
                            echo '</ul>
                                 </div>
                                 <div class="row-fluid">
                                 <ul class="thumbnails">';
                        }//end row and start new row after 4
                    }//end foreach dish
                }
                break;
            case "via_dish":
                echo 'Dishes matching '.$_POST['query'];
                $dishes = getRecipeLikeName($_POST['query']);
                foreach($dishes as $dish){
                    $d_counter +=1;
                    $d_name = $dish['name'];
                    $d_id = $dish['id'];
                    $d_flags = getRecipeFlags($d_id);
                    $d_thumb = (count($d_flags)>=5) ? "default_recipe_thumb.png" : ( (strlen($dish['thumb_url'])>0) ? $dish['thumb_url'] : "default_recipe_thumb.png");
                    echo formatDishSearchResult($dish);
                    include $_SERVER['DOCUMENT_ROOT']."/recipe-gallery-modal.php";
                    if($d_counter%4 == 0){
                        echo '</ul>
                             </div>
                             <div class="row-fluid">
                             <ul class="thumbnails">';
                    }//end row and start new row after 4
                }//end foreach dish
                break;
            default:
                echo 'Unknown search type. Please try again.';
                break;
        }
        echo '</li>';
    }
	if($_POST['create_blog_post']){
		$r_id = getMemberRestaurantID($_SESSION['username']);
		createRestaurantBlogPost($r_id, $_POST['blog_title'], $_POST['blog_body']);
		if(getRestaurantBlogPostByUnix(time(), $r_id)){
			echo '1';
		}
	}
	if($_POST['update_r_seats']){
		setRestaurantSeatCount($_SESSION['username'], $_POST['update_r_seats']);
		if(getRestaurantSeatCount($_SESSION['username']) == $_POST['update_r_seats']){
			echo '1';
		}
	}
    if($_POST['flag_recipe']){
        if(!getDidMemberFlagRecipe($_SESSION['username'], $_POST['flag_recipe'])){
         flagRecipe($_POST['flag_recipe'], $_SESSION['username']);
         if(getDidMemberFlagRecipe($_SESSION['username'], $_POST['flag_recipe'])){
            echo '1';
         }
        }//end if member didn't already flag recipe
        else{
            echo '2';
        }
    }//if recipe flagged

    if($_POST['follow_user']){
        addToFollowers($_SESSION['username'], $_POST['follow_user']);
        if(getIsMemberFollowing($_SESSION['username'], $_POST['follow_user'])){
            echo '1';
        }
    }//if follow request made

    if($_POST['unfollow_user']){
        removeFromFollowers($_SESSION['username'], $_POST['unfollow_user']);
        if(!getIsMemberFollowing($_SESSION['username'], $_POST['unfollow_user'])){
            echo '1';
        }
    }//if unfollow request made

    if($_POST['remove_user']){
        removeFromFollowers($_POST['remove_user'], $_SESSION['username']);
        if(!getIsMemberFollowing($_POST['remove_user'], $_SESSION['username'])){
            echo '1';
        }
    }//if remove from followers request

    if($_POST['update_about_me']){
        setMemberAboutMe($_SESSION['username'], $_POST['update_about_me']);
            echo '1';
    }//if about me updated

    if($_POST['update_now_eating']){
        setMemberNowEating($_SESSION['username'], $_POST['update_now_eating']);
            echo '1';
    }//if about me updated

    if($_POST['update_user_location'] && $_POST['update_user_city'] && $_POST['update_user_state']){
        setMemberCity($_SESSION['username'], ucwords($_POST['update_user_city']));
        setMemberState($_SESSION['username'], strtoupper($_POST['update_user_state']));
        if(getMemberCity($_SESSION['username']) == ucwords($_POST['update_user_city']) && getMemberState($_SESSION['username']) == strtoupper($_POST['update_user_state'])){
            echo '1';
        }
    }//if about me updated

    if($_POST['update_profile_link']){
        setMemberProfileLink($_SESSION['username'], $_POST['update_profile_link']);
            echo '1';
    }//if profile link updated
	if($_POST['update_r_phone']){
        echo setRestaurantPhoneNumber($_SESSION['username'], $_POST['update_r_phone']);
    }//if restaurant phone# updated
	
    if($_POST['favorite_recipe']){
        addRecipeToFavorites($_POST['favorite_recipe'], $_SESSION['username']);
        echo '1';
    }
    if($_POST['unfavorite_recipe']){
        removeRecipeFromFavorites($_POST['unfavorite_recipe'], $_SESSION['username']);
        echo '1';
    }
    if($_POST['add_recipe_comment']){
        addRecipeComment($_POST['add_recipe_comment'], $_POST['body'], $_SESSION['username']);
        echo '1';
    }
    if($_POST['rate_recipe']){
        echo rateRecipe($_POST['id'], $_SESSION['username'], $_POST['rate_recipe']);
    }
    if($_POST['rate_restaurant']){
        echo rateRestaurant($_POST['id'], $_SESSION['username'], $_POST['rate_restaurant']);
    }

    if($_POST['view_msg']){
        echo readMessage($_POST['view_msg']);
    }
    if($_POST['view_msg_thread']){
        echo readThread($_POST['view_msg_thread']);
    }
    if($_POST['view_sent_msg']){
        echo isValidMessage($_POST['view_sent_msg']);
    }
    if($_POST['reply_msg']){
        sendMessage($_POST['reply_sub'], $_POST['reply_msg'], $_SESSION['username'], $_POST['reply_to']);
        echo '1';
    }
     
}//if logged in
?>