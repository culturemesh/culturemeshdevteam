<label class="label label-success hide" id="account_info_update_success_txt">Information successfully updated!</label>
<label class="label label-important hide" id="account_info_update_failure_txt">Update failed. Please try again.</label>
<form id="account_info_form">
<input type="hidden" name="ai_update" value="1"/>
<input type="hidden" name="c_pw" id="c_pw" />
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

<h5>Send me emails when</h5>
<label class="checkbox"><input type="checkbox" name="notify_interesting_events" <?=getCheckboxVal(getMemberNotificationSettingsInterestingEvents($_SESSION['uid']));?>>CultureMesh finds events I'd be interested in near me</label>
<label class="checkbox"><input type="checkbox" name="notify_company_news" <?=getCheckboxVal(getMemberNotificationSettingsCompanyNews($_SESSION['uid']));?>>CultureMesh has fun company news</label>
<label class="checkbox"><input type="checkbox" name="notify_events_upcoming" <?=getCheckboxVal(getMemberNotificationSettingsUpcomingEvents($_SESSION['uid']));?>>I have an upcoming event</label>
<label class="checkbox"><input type="checkbox" name="notify_network_activity" <?=getCheckboxVal(getMemberNotificationSettingsNetworkActivity($_SESSION['uid']));?>>I have received comments to a network event I added</label>
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
    $("#password_confirm_btn").click(function(){
        $("#c_pw").val($("#current_password").val());
        $.post("ajx/ps.php", $("#account_info_form").serialize())
        .done(function(data){
            switch(data){
            case "1":
                $("#current_password").val("");
                $("#current_password_failure_txt").hide();
                $("#invalid_email_txt").hide();
                $("#<?=PASSWORD_CONFIRM_MODAL_ID?>").modal("hide");
                $("#account_info_update_success_txt").fadeIn();
                delay(function(){$("#account_info_update_success_txt").fadeOut()}, 2000);
                break;
            case "2":
                $("#current_password_failure_txt").show();
                break;
            case "3":
                $("#invalid_email_txt").show();
                break;
            }
        });
    });
</script>