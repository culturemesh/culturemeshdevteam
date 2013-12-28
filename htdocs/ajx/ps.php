<?php
require '../log.php';

if($_POST['reg_email']){
    echo getIsEmailAvailable($_POST['reg_email']);
}//checking registration email
    
if($_SESSION['uid']){
    
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