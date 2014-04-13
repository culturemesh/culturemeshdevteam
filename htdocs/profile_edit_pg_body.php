<?php
include_once 'zz341/fxn.php';
include_once 'html_builder.php';
include_once 'data/dal_user.php';
include_once 'data/dal_user_info.php';
include_once 'data/dal_user_notification.php';
include_once 'data/dal_event.php';
include_once 'data/dal_event_registration.php';
include_once 'data/dal_network.php';
include_once 'data/dal_network_registration.php';
include_once 'data/dal_post.php';
include_once 'data/dal_conversation.php';

$con = getDBConnection();

$user = User::getUserById($_SESSION['uid'], $con);
$info = UserInfo::getInfoByUser($_SESSION['uid'], $con);
?>
<?php
$pass_header = '<b>Change Password</b>';
$pass_body = '
<form id="password_change_form">
	<div>
	<input type="hidden" name="pi_update" value="1"/>
	<input type="hidden" name="c_pw" id="c_pw" />
	<input type="email" name="email" id="password_email" class="dashboard" value="'.getMemberEmail($_SESSION['uid']).'" placeholder="Email Address"></br>
	<input type="password" name="cur_password" id="password_cur" class="dashboard" placeholder="Current Password"/></br>
	<input type="password" name="password" id="password" class="dashboard" placeholder="New Password"/></br>
	<input type="password" name="password_conf" id="password_conf" class="dashboard" placeholder="Confirm Password"/></br>
	<label class="label label-important hide" id="password_mismatch_txt">Passwords do not match. Please re-enter passwords to match.</label></br>
	</div>
	<div class="clear"></div>
	<input type="submit" id="cp_submit_btn" class="btn cm-button btn-green dash" data-loading-text="Checking..." value="Change Password" /></br>
</form>
';
$pass_footer = '';
echo buildModal($pass_header, $pass_body, $pass_footer, "password_confirm_modal");?>
<script>
    $("#password_conf").change(function(){
        if($("#password").val() != $("#password_conf").val()){
            $("#password_mismatch_txt").show();
        }
        else{
            $("#password_mismatch_txt").hide();
        }
    });
    $("#cp_submit_btn").click(function(e){
	e.preventDefault();
        $("#c_pw").val($("#current_password").val());
        $.post("profile_operations.php", $("#password_change_form").serialize())
        .done(function(data){
	    var results = JSON.parse(data);
	    $("#password_mismatch_txt").text(results['msg']);
	    $("#password_mismatch_txt").show();
	    /*
            switch(results){
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
	     */
	})
	.fail(function(data) {
		$("#password_mismatch_txt").text("We're having technical problems on our end. Try again later.");
	});
   });
</script>
<div id="profile_edit_modal" class="modal hide fade" tabindex="-1" role="dialog"  aria-labelledby="blogPostLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
		<b>Basic Info</b>  
        </div>
	<div class="modal-body">
		<label class="label label-success hide" id="basic_info_update_success_txt">Information successfully updated!</label>
		<label class="label label-important hide" id="basic_info_update_failure_txt">Update failed. Please try again.</label>
		<form id="basic_info_form">
			<table class="profile dashboard">
			<thead></thead>
			<tfoot><tfoot>
			<tbody>
				<input type="hidden" name="bi_update" value="1"/>
				<tr class="dashboard">
					<!--<td class="dashboard"><h3 class="dashboard">First Name:</h3></td>-->
					<td class="dashboard"><input class="dashboard" type="text" name="first_name" value="<?=getMemberFirstName($_SESSION["uid"])?>" placeholder="First Name"></td>
				</tr>
				<tr class="dashboard">
					<!--<td class="dashboard"><h3 class="dashboard">Last Name:</h3></td>-->
					<td class="dashboard"><input class="dashboard" type="text" name="last_name" value="<?=getMemberLastName($_SESSION["uid"])?>" placeholder="Last Name"></td>
				</tr>
				<tr class="dashboard">
					<!--<td class="dashboard"><h3 class="dashboard">Gender:</h3></td>-->
					<td class="dashboard">
					
					    <select class="dashboard" name="gender">
						<option '.<?php if(getMemberGender($_SESSION["uid"]) == "m"){echo "selected";}?>'>Male</option>
						<option '<?php if(getMemberGender($_SESSION["uid"]) == "f"){echo "selected";}?>'>Female</option>
					    </select>
					</td>
				</tr>
				<tr class="dashboard">
					<!--<td class="dashboard"><h3 class="dashboard">About Me:</h3></td>-->
					<td class="dashboard"><textarea class="dashboard" name="about_me" placeholder="Tell us about yourself..."><?=getMemberAboutMe($_SESSION["uid"])?></textarea></td>
				</tr>
			</tbody>
			</table>
			<a class="btn cm-button btn-green dash" id="basic_info_update_btn">Update</a>
		</form>
	</div>
	<div class="modal-footer">
	</div>
</div>
<script>
    // submit basic info to db
    $("#basic_info_update_btn").click(function(){
        $.post("profile_operations.php", $("#basic_info_form").serialize())
        .done(function(data){
	    results = JSON.parse(data);
            if(results['error'] == 0){
		// update stuff
		$("#profile_name").text(results["first_name"] + " " + results["last_name"]);
		$("#profile_about").text(results["about_me"]);
                $("#basic_info_update_success_txt").fadeIn();
                delay(function(){$("#basic_info_update_success_txt").fadeOut()}, 2000);
            }
	})
	.fail(function(data, ajaxOptions, thrownError) {
		alert(data.status);
		alert(thrownError);
	});
    });
</script>
<div class="profile_left_panel">
    <span class="profile_image">
    	<img src="images/blank_profile_lrg.png"/>
    </span>
    </br>
    <span class="profile_name">
    	<h2 id="profile_name" class="dashboard"><?php echo $info->first_name . " " . $info->last_name ; ?></h2>
<!--	<p id="profile_gender" class="profile"><?php //echo $info->gender; ?></p>-->
	<p id="profile_about" class="profile"><?php echo $info->about_me; ?></p>
    </span>
    <p class="profile"><a href="#profile_edit_modal" class="profile" id="pro-edit-link" data-toggle="modal">Edit Profile</a></p>
    <p class="profile"><a data-toggle="modal" href="#password_confirm_modal">Change Password</a></p>
</div>
<div class="profile_dashboard">
    <?php HTMLBuilder::displaySearchBar(); ?>
    <ul class="nav nav-pills dashboard">
<!--        <li class="active"><a href="#profile_basic_info_tab" data-toggle="pill">Basic Info</a></li>-->
        <li class="active"><a href="#profile_dashboard_tab" data-toggle="pill">Dashboard</a></li>
        <li><a href="#profile_networks_tab" data-toggle="pill">Networks</a></li>
        <li><a href="#profile_events_tab" data-toggle="pill">Events</a></li>
<!--        <li><a href="#profile_inbox_tab" data-toggle="pill">Inbox</a></li>-->
        <li><a href="#profile_accounts_tab" data-toggle="pill">Account</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane" id="profile_basic_info_tab">
            <?php include 'profile_basic_info_tab_include.php'; ?>
        </div>
        <div class="tab-pane active" id="profile_dashboard_tab">
            <?php include 'profile_dashboard_tab_include.php'; ?>
        </div>
        <div class="tab-pane" id="profile_networks_tab">
            <?php include 'profile_networks_tab_include.php'; ?>
        </div>
        <div class="tab-pane" id="profile_events_tab">
            <?php include 'profile_events_tab_include.php'; ?>
        </div>
        <div class="tab-pane" id="profile_inbox_tab">
            <?php include 'profile_inbox_tab_include.php'; ?>
        </div>
        <div class="tab-pane" id="profile_accounts_tab">
            <?php include 'profile_accounts_tab_include.php'; ?>
        </div>
    </div>
</div>
<div class="clear"></div>
<script src="js/searchbar.js"></script>
<?php mysqli_close($con); ?>
