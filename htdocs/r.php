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
        }//db connect failure
        $email = $conn->real_escape_string($_POST['email']);
        $data = getRowQuery("SELECT * FROM users WHERE email='{$email}'");

        if(!$data){
            echo 'inw';
            if(actionQuery("INSERT INTO users (email,role,password,register_date,last_login) values(
                '{$email}',0,'".md5($_POST['password'])."', NOW(), NOW())")){
                session_name("myDiaspora");
                session_start();
                $_SESSION['uid'] = getMemberUID($email);
                header("Location: profile_edit.php");
            }
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
