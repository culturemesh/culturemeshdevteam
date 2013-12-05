<?php
session_name("myDiaspora");
session_start();
//$admins = array("jenki221@msu.edu", "jenki221@umich.edu", "admin@msu.edu", "professorkos@msu.edu", "jenki221", "jenki221@stanford.edu");

if($_GET['action'] == "login") {
$username = $_POST['username'];
$conn = new mysqli("localhost","culturp7_ktc","d4T48@$3", "culturp7_ktc");
if($conn->connect_errno){
    printf("connect failed %s\n", $conn->connect_error);
    exit();
}
$uQuery = $conn->query("SELECT * FROM users WHERE username='$username'");
$data = $uQuery->fetch_assoc();
if($data != NULL) {
    if($data['role'] == NULL){$conn->query("UPDATE users SET role='user' WHERE username='$username'");}
    if(md5($_POST['password']) == $data['password']) {
		/*if($confirmation == "no"){
			exit("Your account is currently NOT CONFIRMED. You must confirm your account with the confirmation link sent to your email to use the site. 
				<br>
				 If you need your confirmation link sent to your email again, <a href='resendconfirmation.php'>click here</a>.");
		}//end if account not confirmed
		else{*/
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $data['role'];
        $conn->query("UPDATE users SET last_login = '".time()."' WHERE username='$username'");
        if(strtolower($_SESSION['gender']) == "male"){
                $GLOBALS["possesivepronoun"] = "his";
        }//end if male
        else if(strtolower($_SESSION['gender']) == "female"){
                $GLOBALS["possesivepronoun"] = "her";
        }//end else if female
        else{
                $GLOBALS["possesivepronoun"] = "their";
        }//end else if unknown gender

        if($_POST["redirecttolink"]){
                echo '<meta http-equiv="refresh" content="0; url='.$_POST['redirecttolink'].'"/>';
        }//enf if redirected
        else{
                header("Location: index.php"); // success page. put the URL you want
        }//end else if not redirected
        exit;
    //}//end else if account confirmed
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

include 'zz341/fxn.php';
//include 'static/classes.php';
?>