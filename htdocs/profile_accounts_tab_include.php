<?php
/*
$pass_header = '<b>Change Password</b>';
$pass_body = '
<form id="password_change_form" action="change_password.php" method="POST">
	<input type="hidden" name="ai_update" value="1"/>
	<input type="hidden" name="c_pw" id="c_pw" />
	<input type="email" name="email" value="'.getMemberEmail($_SESSION['uid']).'" placeholder="Email Address"></br>
	<input type="password" name="cur_password" placeholder="Current Password"/></br>
	<input type="password" name="password" id="password" placeholder="New Password"/></br>
	<input type="password" name="password_conf" id="password_conf" placeholder="Confirm Password"/></br>
	<label class="label label-important hide" id="password_mismatch_txt">Passwords do not match. Please re-enter passwords to match.</label></br>
	<input type="submit" id="cp_submit_btn" class="btn cm-button btn-green dash" data-loading-text="Checking..." value="Change Password" /></br>
</form>
';
$pass_footer = '';
echo buildModal($pass_header, $pass_body, $pass_footer, "password_confirm_modal");
 */
?>
<!--
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
                $("#<?//=PASSWORD_CONFIRM_MODAL_ID?>").modal("hide");
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
-->
<form id="account_info_form" method="POST" action="update_notifications.php">
<div>
	<label class="label label-success hide" id="account_info_update_success_txt">Information successfully updated!</label>
	<label class="label label-important hide" id="account_info_update_failure_txt">Update failed. Please try again.</label>
	<h5>Send me emails when</h5>
	<label class="checkbox"><input type="checkbox" name="notify_interesting_events" value="1" <?=getCheckboxVal($user->events_interested_in);?>>CultureMesh finds events I'd be interested in near me</label>
	<label class="checkbox"><input type="checkbox" name="notify_company_news" value="1" <?=getCheckboxVal($user->company_news);?>>CultureMesh has fun company news</label>
	<label class="checkbox"><input type="checkbox" name="notify_events_upcoming" value="1" <?=getCheckboxVal($user->events_upcoming);?>>I have an upcoming event</label>
	<label class="checkbox"><input type="checkbox" name="notify_network_activity" value="1" <?=getCheckboxVal($user->network_activity);?>>I have received comments to a network event I added</label>
	<input type="hidden" name="notification" value="yes"/>
</div>

<?php if ($user->confirmed == 1) : ?>
<div id="account-buttons">
	<!--<input type="submit" class="btn cm-button btn-gray dash" value="Cancel"\>-->
	<input type="submit" class="btn cm-button btn-green dash" value="Submit"\>
</div>

<?php else : ?>
<div>
	<p>You must confirm your email to submit these changes.</p>
</div>
<?php endif; ?>

</form>
