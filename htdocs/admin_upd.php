<?php
require_once 'log.php';

$uid = $_SESSION['uid'];

if($_POST['careers_tab']){
    if($_POST['job_title'] && $_POST['job_desc']){
        actionQuery("INSERT INTO internal_careers (job_title,job_description,date_posted) values(
                    '{$_POST['job_title']}',
                    '{$_POST['job_desc']}',
                    ".time().")");
    }
}//end careers tab

if($_POST['team_tab']){
    if($_FILES['member_thumb']['error'] > 0){
        //echo 'error'.$_FILES['media']['error'];
    }
    else{
        $pname = explode(" ", $_POST['member_name']);
        $temp = explode(".", $_FILES["member_thumb"]["name"]);
        $extension = end($temp);
        $media = $pname[0].'_'.$pname[1].'.'.$extension;
        if(!is_dir('internal/team')){
            mkdir('internal/team',0755,true);
        }
        move_uploaded_file($_FILES['member_thumb']['tmp_name'], 'internal/team/'.$media);
        $url = 'http://www.culturemesh.com/internal/team/'.$media;
    }
    if($_POST['member_name'] && $_POST['member_title'] && $_POST['member_bio']){
        actionQuery("INSERT INTO internal_team (name,job_title,bio,team_member_since,thumb_url) values(
                    '{$_POST['member_name']}',
                    '{$_POST['member_title']}',
                    '{$_POST['member_bio']}',
                    ".time().",
                    '{$url}')");
    }
}//end team member tab

if($_POST['press_tab']){
    if($_FILES['press_thumb']['error'] > 0){
        //echo 'error'.$_FILES['media']['error'];
    }
    else{
        $temp = explode(".", $_FILES["press_thumb"]["name"]);
        $extension = end($temp);
        $media = md5(rand(1,5)*time()).'.'.$extension;
        if(!is_dir('internal/press')){
            mkdir('internal/press',0755,true);
        }
        move_uploaded_file($_FILES['press_thumb']['tmp_name'], 'internal/press/'.$media);
        $url = 'http://www.culturemesh.com/internal/press/'.$media;
    }
    if(isAdmin($uid) && $_POST['press_title'] && $_POST['press_subtitle'] && $_POST['press_body']){
        actionQuery("INSERT INTO internal_press (title,sub_title,body,thumb_url,date) values(
                    '{$_POST['press_title']}',
                    '{$_POST['press_subtitle']}',
                    '{$_POST['press_body']}',
                    '{$url}',
                    ".time().")");
    }
}//end press tab
if($_POST['aep_edit_id']){
    if($_FILES['aep_edit_thumb']['error'] > 0){
        //echo 'error'.$_FILES['media']['error'];
    }
    else{
        $temp = explode(".", $_FILES["aep_edit_thumb"]["name"]);
        $extension = getExt($_FILES["aep_edit_thumb"]["name"]);//end($temp);
        $media = md5(rand(1,5)*time()).'.'.$extension;
        if(!is_dir('internal/press')){
            mkdir('internal/press',0755,true);
        }
        move_uploaded_file($_FILES['aep_edit_thumb']['tmp_name'], 'internal/press/'.$media);
        $url = 'http://www.culturemesh.com/internal/press/'.$media;
        actionQuery("UPDATE internal_press SET thumb_url='{$url}' WHERE id={$_POST['aep_edit_id']}");
    }
    if(isAdmin($uid) && $_POST['aep_edit_title'] && $_POST['aep_edit_subtitle'] && $_POST['aep_edit_body']){
        actionQuery("UPDATE internal_press SET title='{$_POST['aep_edit_title']}',
                    sub_title='{$_POST['aep_edit_subtitle']}',
                    body='{$_POST['aep_edit_body']}',
                    last_updated=".time()." WHERE id={$_POST['aep_edit_id']}");
    }
}//end if editing post
if($_POST['aet_edit_id']){
    if($_FILES['aet_edit_thumb']['error'] > 0){
        //echo 'error'.$_FILES['media']['error'];
    }
    else{
        $pname = explode(" ", $_POST['aet_edit_name']);
        $extension = getExt($_FILES["aet_edit_thumb"]["name"]);//end($temp);
        $media = $pname[0].'_'.$pname[1].'.'.$extension;
        if(!is_dir('internal/team')){
            mkdir('internal/team',0755,true);
        }
        move_uploaded_file($_FILES['aet_edit_thumb']['tmp_name'], 'internal/team/'.$media);
        $url = 'http://www.culturemesh.com/internal/team/'.$media;
        actionQuery("UPDATE internal_team SET thumb_url='{$url}' WHERE id={$_POST['aet_edit_id']}");
    }
    if(isAdmin($uid) && $_POST['aet_edit_title'] && $_POST['aet_edit_name'] && $_POST['aet_edit_bio']){
        actionQuery("UPDATE internal_team SET job_title='{$_POST['aet_edit_title']}',
                    name='{$_POST['aet_edit_name']}',
                    bio='{$_POST['aet_edit_bio']}',
                    team_member_since=".time()." WHERE id={$_POST['aet_edit_id']}");
    }
}//end if editing team member
if($_POST['aec_edit_id']){
    if(isAdmin($uid) && $_POST['aec_edit_title'] && $_POST['aec_edit_desc']){
        actionQuery("UPDATE internal_careers SET job_title='{$_POST['aec_edit_title']}',
                    job_description='{$_POST['aec_edit_desc']}',
                    date_posted=".time()." WHERE id={$_POST['aec_edit_id']}");
    }
}//end if editing career

if($_POST['adp_delete_id']){
    $post = getRowQuery("SELECT * FROM internal_press WHERE id={$_POST['adp_delete_id']}");
    $url = $post['thumb_url'];
    if(file_exists('internal/press/'.getBaseName($url))){
        unlink('internal/press/'.getBaseName($url));
    }
    actionQuery("DELETE FROM internal_press WHERE id={$_POST['adp_delete_id']}");
}//end if deleting press post
if($_POST['adt_delete_id']){
    $member = getRowQuery("SELECT * FROM internal_team WHERE id={$_POST['adt_delete_id']}");
    $url = $member['thumb_url'];
    if(file_exists('internal/team/'.getBaseName($url))){
        unlink('internal/team/'.getBaseName($url));
    }
    actionQuery("DELETE FROM internal_team WHERE id={$_POST['adt_delete_id']}");
}//end if deleting team member
if($_POST['adc_delete_id']){
    $member = getRowQuery("SELECT * FROM internal_careers WHERE id={$_POST['adc_delete_id']}");
    actionQuery("DELETE FROM internal_careers WHERE id={$_POST['adc_delete_id']}");
}//end if deleting career
echo 'Successfully updated! Refreshing...<meta http-equiv="refresh" content="2;url=http://www.culturemesh.com/admin.php">';