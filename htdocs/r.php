<?php
if(strlen($_POST['email'])>1 && strlen($_POST['password']) >=6 && strlen($_POST['password_conf'])>=6){
    if($_POST['password'] != $_POST['password_conf']){
        header("Location: registration_error.php?error=password_mismatch");
    }//mismatch password
    else{
        include 'zz341/fxn.php';
        $conn = getDBConnection();
        if($conn->connect_errno){
            printf("Connect failed! %s\n", $conn->connect_error);
            exit();
        }//db conect failure
        $email = $conn->real_escape_string($_POST['email']);
        $uQuery = $conn->query("SELECT * FROM users WHERE email_address='$email'");
        $data = $uQuery->fetch_assoc();
        if(!$data){
            $conn->query("INSERT INTO users (email_address,role,password,date_joined,last_login) values(
                '$email','user',".md5($_POST['password'])."','".time()."','".time()."')");
            header("Location: profile_edit.php");
        }//valid new user
        else{
            header("Location: registration_error.php?error=user_exists");
        }//username already exists
    }
}
else{
    header("Location: index.php");
}
?>