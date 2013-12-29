<label class="label label-success hide" id="basic_info_update_success_txt">Information successfully updated!</label>
<label class="label label-important hide" id="basic_info_update_failure_txt">Update failed. Please try again.</label>
<form id="account_info_form">
<input type="hidden" name="ai_update" value="1"/>
<label>Email Address
    <input type="email" name="email" value="<?=getMemberEmail($_SESSION['uid'])?>" placeholder="Email Address">
</label>
<label>Set New Password
    <input type="password" name="password" id="password" placeholder="New Password">
</label>
<label>Re-enter New Password
    <input type="password" name="password_conf" id="password_conf" placeholder="Re-enter New Password">
</label>
<label class="label label-important hide" id="password_mismatch_txt">Passwords do not match. Please re-enter passwords to match.</label>
</form>
<?=buildPasswordConfirmModal()?>
<a class="btn cm-button btn-green" data-toggle="modal" href="#<?=PASSWORD_CONFIRM_MODAL_ID?>">Update</a>
<script>
    $("#password_conf").change(function(){
        if($("#password").val() != $("#password_conf").val()){
            $("#password_mismatch_txt").show();
        }
        else{
            $("#password_mismatch_txt").hide();
        }
    });
    $("#account_info_update_btn").click(function(){
        $.post("ajx/ps.php", $("#account_info_form").serialize())
        .done(function(data){
            if(data == "1"){
                $("#basic_info_update_success_txt").fadeIn();
                delay(function(){$("#basic_info_update_success_txt").fadeOut()}, 2000);
            }
        });
    });
</script>