<?php
require '../log.php';

if($_POST['reg_email']){
    echo getIsEmailAvailable($_POST['reg_email']);
}//checking registration email
    
if($_SESSION['uid']){
    $uid = $_SESSION['uid'];
    //if updating account info(user dashboard)
    if($_POST['ai_update']){
        //make sure password entered matches current password to save changes
        if(md5($_POST['c_pw']) == getMemberPassword($uid)){
            //if changing email address
            if(verifyValidEmailAddress($_POST['email']) && $_POST['email'] != getMemberEmail($uid)){
                $code = actionQuery("UPDATE users SET email='{$_POST['email']}' WHERE id={$uid}");
            }
            else{
                //invalid email address
                $code = "3";
                exit($code);
            }
            //if changing password
            if(verifyPasswordMatch($_POST['password'],$_POST['password_conf'])){
                $code = actionQuery("UPDATE users SET password='".md5($_POST['password'])."' WHERE id={$uid}");
            }
            require '../static/classes/UserSettings.php';
            $u_settings = new UserSettings($uid);
            $u_settings->setEventsInterested(getCheckboxBool($_POST['notify_interesting_events']));
            $u_settings->setCompanyNews(getCheckboxBool($_POST['notify_company_news']));
            $u_settings->setEventsUpcoming(getCheckboxBool($_POST['notify_events_upcoming']));
            $u_settings->setNetworkActivity(getCheckboxBool($_POST['notify_network_activity']));
            $code = (intval($code) >= 1) ? $code : $u_settings->Save();
            echo $code;
        }
        else{
            $code = "2";
            echo $code;
        }
    }
    //if updating basic info(user dashboard)
    if($_POST['bi_update']){
        //if data for user already exists
        if(getRowQuery("SELECT * FROM user_info WHERE uid={$uid}")){
            echo actionQuery("UPDATE user_info SET "
                    . "first_name='".ucwords(strtolower($_POST['first_name']))."',"
                    . "last_name='".ucwords(strtolower($_POST['last_name']))."',"
                    . "gender='".strtolower($_POST['gender'][0])."',"
                    . "about_me='{$_POST['about_me']}'");
        }
        //if not,insert fresh data for user
        else{
            echo actionQuery("INSERT INTO user_info(uid,first_name,last_name,gender,about_me) values(
                    {$uid},
                    '".ucwords(strtolower($_POST['first_name']))."',
                    '".ucwords(strtolower($_POST['last_name']))."',
                    '".strtolower($_POST['gender'][0])."',
                    '{$_POST['about_me']}')");
        }
    }
    /***start admin posts******/
    //if(getIsMemberAdmin($_SESSION['uid']){//check if logged-in user is an admin
    if($_POST['admin_remove_network'] && $_POST['admin_remove_network_type']){
        echo actionQuery("DELETE FROM suggested_networks WHERE ".$_POST['admin_remove_network_type']."='".$_POST['admin_remove_network']."'");
    }
    //if admin adding network
    if($_POST['admin_attr']){
        require('../static/classes/Network.php');
        $n_network = new Network();
        switch(strtolower($_POST['admin_attr'])){
            case "language":
                $n_network->setLanguage(ucwords(strtolower($_POST['admin_attr_name'])));
                echo $n_network->Save();
                break;
            case "city":
                $n_network->setCity(ucwords(strtolower($_POST['admin_attr_name'])));
                $n_network->setRegion(ucwords(strtolower($_POST['admin_network_region'])));
                $n_network->setCountry($_POST['admin_network_country']);
                echo $n_network->Save();
                break;
        }
    }
    //}
    /***end admin requests******/
     
}//if logged in
?>