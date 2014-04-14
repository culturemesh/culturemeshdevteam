<?php
session_name("myDiaspora");
session_start();
//$admins = array("jenki221@msu.edu", "jenki221@umich.edu", "admin@msu.edu", "professorkos@msu.edu", "jenki221", "jenki221@stanford.edu");
include_once 'zz341/fxn.php';
//include 'static/classes.php';

if($_GET['action'] == "login") {
    $conn = getDBConnection();
    $email = $conn->real_escape_string($_POST['email']);
    $data = getRowQuery("SELECT * FROM users WHERE email='{$email}'");
    if($data) {
        if($data['role'] == NULL){
            $conn->query("UPDATE users SET role=0 WHERE email='$email'");
        }
        if(md5($_POST['password']) == $data['password']) {
            $_SESSION['uid'] = $data['id'];
            $conn->query("UPDATE users SET last_login = NOW() WHERE email='$email'");
            switch(strtolower(getMemberGender($_SESSION['uid']))){
                case "male":
                    $GLOBALS["possesivepronoun"] = "his";
                    break;
                case "female":
                    $GLOBALS["possesivepronoun"] = "her";
                    break;
                default:
                    $GLOBALS["possesivepronoun"] = "their";
                    break;
            }
            if($_POST["redirecttolink"]){
                echo '<meta http-equiv="refresh" content="0; url='.$_POST['redirecttolink'].'"/>';
            }//enf if redirected
            else{
                    header("Location: profile_edit.php"); // success page. put the URL you want
            }//end else if not redirected
            exit;
        } //end if pasword matches
        else {
            if($_POST["redirecttolink"]){
                header("Location: ?login=failed&cause=".urlencode('Wrong Password')."&redirecttolink=".urlencode($_POST['redirecttolink']));
                echo 'Invalid Password';
            }//end if redirected
            else{
                echo 'Invalid Password';
                include 'login.php';
            }//end else if no redirectlinkposted
            exit;
        }//end else if password doesnt match(incorrect password)
    }//end if query matches 
    else {
        if($_POST["redirecttolink"]){
            header("Location: ?login=failed&cause=".urlencode('Invalid User')."&redirecttolink=".urlencode($_POST['redirecttolink']));
            echo 'Invalid User';
        }//enf if redirected
        else{
            echo 'Invalid User';
            include 'login.php';
        }//end else if noredirectlinkposted
        exit;
    }//end else if query doesn't match(invalid user)
}//end if action is get
?>