<label class="label label-success hide" id="basic_info_update_success_txt">Information successfully updated!</label>
<label class="label label-important hide" id="basic_info_update_failure_txt">Update failed. Please try again.</label>
<form id="basic_info_form">
    <input type="hidden" name="bi_update" value="1"/>
<label>First Name
    <input type="text" name="first_name" value="<?=getMemberFirstName($_SESSION['uid'])?>" placeholder="First Name">
</label>
<label>Last Name
    <input type="text" name="last_name" value="<?=getMemberLastName($_SESSION['uid'])?>" placeholder="Last Name">
</label>
<label>Gender
    <select name="gender">
        <option <?php if(getMemberGender($_SESSION['uid']) == 'm'){echo 'selected';}?>>Male</option>
        <option <?php if(getMemberGender($_SESSION['uid']) == 'f'){echo 'selected';}?>>Female</option>
    </select>
</label>
<label>About Me
    <textarea name="about_me" placeholder="Tell us about yourself..."><?=getMemberAboutMe($_SESSION['uid'])?></textarea>
</label>
</form>
<a class="btn cm-button btn-green" id="basic_info_update_btn">Update</a>
<script>
    $("#basic_info_update_btn").click(function(){
        $.post("ajx/ps.php", $("#basic_info_form").serialize())
        .done(function(data){
            if(data == "1"){
                $("#basic_info_update_success_txt").fadeIn();
                delay(function(){$("#basic_info_update_success_txt").fadeOut()}, 2000);
            }
        });
    });
</script>