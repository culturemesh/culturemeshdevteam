<?php

if($_POST['email'] && $_POST['password'] && $_POST['password_conf']){
    if($_POST['password'] != $_POST['password_conf']){
        header("Location: registration_error.php?error=password_mismatch");
    }//mismatch password
    else{
        include 'zz341/fxn.php';
        $conn = getDBConnection();
        $email = escape_string($_POST['email']);
        echo $email;
        if($conn->connect_errno){
            printf("Connect failed! %s\n", $conn->connect_error);
            exit();
        }//db conect failure
        $uQuery = $conn->query("SELECT * FROM users WHERE email_address='$email'");
        $data = $uQuery->fetch_assoc();
        var_dump($data);
        if(!$data){
            $conn->query("INSERT INTO users (email_address,password,date_joined,last_login) values(
                '$email','".md5($_POST['password'])."','".time()."','".time()."')");
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