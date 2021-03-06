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

// DATA LOAAADD!!!
// // will probably shorten to one or two
// // queries eventually
$con = getDBConnection();

$user = User::getUserById($_SESSION['uid'], $con);
//$info = UserInfo::getInfoByUser($_SESSION['uid'], $con);

// events you're hosting
$yh_events = Event::getEventsByUserId($_SESSION['uid'], $con);

// events in your network
$yn_events = Event::getEventsYourNetworks($_SESSION['uid'], $con);

// events you're attending
$ya_events = EventRegistration::getEventRegistrationsByUserId($_SESSION['uid'], $con);

// get ids of networks from events
$ye_nids = array();
$all_events = array_merge($yh_events, $yn_events, $ya_events);

// add network id if not already in array
foreach($all_events as $event) {
	if (!in_array($event->id_network, $ye_nids)) {
		array_push($ye_nids, $event->id_network);
	}
}

// post bounds
$post_bounds = array(0,10);
$test_bounds = array(0, 11);

// your posts
$yp_posts = Post::getPostsByUserId($_SESSION['uid'], $test_bounds, $con);



// get ids of networks with posts
$yp_nids = array();
foreach ($yp_posts as $post) {
	array_push($yp_nids, $post->id_network);
}

// your post networks
$yp_networks = Network::getNetworksWithUserPost($yp_nids, $con);

// your event networks
$ye_networks = Network::getNetworksWithEvents($ye_nids, $con);

// your networks
$yn_networks = NetworkRegistration::getNetworksByUserId($_SESSION['uid'], $con);

// and now the herculean task is done
mysqli_close($con);

// check for image link
$img_link = NULL;
if($user->img_link == NULL)
	$img_link = 'images/blank_profile.png';
else
	$img_link = IMG_DIR.$user->img_link;

// runs a linear search through array of networks
function findNetwork($id, $networks) {
	foreach ($networks as $network) {
		if($id == $network->id)
			return $network;
	}
}
?>
<?php
$pass_header = '<b>Change Password</b>';
$pass_body = '
<form id="password_change_form">
	<div>
	<input type="hidden" name="pi_update" value="1"/>
	<input type="hidden" name="c_pw" id="c_pw" />
	<input type="email" name="email" id="password_email" class="dashboard" value="'.$user->email.'" placeholder="Email Address"></br>
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
					<td class="dashboard"><input class="dashboard" type="text" name="first_name" value="<?php echo $user->first_name;?>" placeholder="First Name"></td>
				</tr>
				<tr class="dashboard">
					<!--<td class="dashboard"><h3 class="dashboard">Last Name:</h3></td>-->
					<td class="dashboard"><input class="dashboard" type="text" name="last_name" value="<?php echo $user->last_name;?>" placeholder="Last Name"></td>
				</tr>
				<tr class="dashboard">
					<!--<td class="dashboard"><h3 class="dashboard">Gender:</h3></td>-->
					<td class="dashboard">
					
					    <select class="dashboard" name="gender">
						<option '.<?php if($user->gender == "m"){echo "selected";}?>'>Male</option>
						<option '<?php if($user->gender == "f"){echo "selected";}?>'>Female</option>
					    </select>
					</td>
				</tr>
				<tr class="dashboard">
					<!--<td class="dashboard"><h3 class="dashboard">About Me:</h3></td>-->
					<td class="dashboard"><textarea class="dashboard" name="about_me" placeholder="Tell us about yourself..."><?php echo $user->about_me; ?></textarea></td>
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
		$("#welcome").text("Welcome, " + results["first_name"]);
                $("#basic_info_update_success_txt").fadeIn();
                delay(function(){$("#basic_info_update_success_txt").fadeOut()}, 2000);
            }
	    else {
		$("#basic_info_update_success_txt").text(results["error"]);
                $("#basic_info_update_success_txt").fadeIn();
                //delay(function(){$("#basic_info_update_success_txt").fadeOut()}, 2000);
	    }
	})
	.fail(function(data, ajaxOptions, thrownError) {
		alert(data.status);
		alert(thrownError);
	});
    });
</script>
<?php if($user->confirmed == 0) :?>
<script>
	function resendEmail(uid) {
		var confirmTxt = document.getElementById("confirm_txt");
		var query = "uid="+uid;

		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200)	{
				confirmTxt.style.display = "block";
			}
		}
		xmlhttp.open("POST", "confirmation_resend.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send(query);
	}
</script>
<div id="confirmation_panel">
	<p>An confirmation email has been sent to you.</p>
	<p>Please respond so that you can enjoy full access to CultureMesh</p>
	<p>If you need another confirmation email, <a href="#" onclick="resendEmail(<?php echo $_SESSION['uid']; ?>)">click here</a></p>
	<p id="confirm_txt" style="display:none;">Confirmation sent</p>
</div>
<?php endif; ?>
<div class="profile_left_panel">
    <span class="profile_image">
    	<img id="profile_image" class='usr_image' src="<?php echo $img_link; ?>#<?php echo rand() ?>"/>
    </span>
    <div id="pic-upload-div">
	    <p id="success-label"></p>
	    <form id="profile-pic-upload" method="POST" action="profile_img_upload.php" enctype="multipart/form-data"/>
		<input type="file" id="upload-input" name="picfile">
		<input type="hidden" id="upload-id" name="id" value="<?php echo $_SESSION["uid"];?>">
		<noscript>
			<input type="submit" value="Upload File">
			<input type="hidden" name="ajax-enabled" value="false">
		</noscript>
	    </form>
    </div>
    </br>
    <span class="profile_name">
    	<h2 id="profile_name" class="dashboard"><?php echo $user->first_name . " " . $user->last_name ; ?></h2>
<!--	<p id="profile_gender" class="profile"><?php //echo $info->gender; ?></p>-->
	<p id="profile_about" class="profile"><?php echo $user->about_me; ?></p>
    </span>
    <p class="profile"><a href="#profile_edit_modal" class="profile" id="pro-edit-link" data-toggle="modal">Edit Profile</a></p>
    <?php if($user->confirmed == 1) : ?>
    <p class="profile"><a data-toggle="modal" href="#password_confirm_modal">Change Password</a></p>
    <?php endif; ?>
    <p class="profile"><a id="pic-upload-toggle" href="#">Upload Picture</a></p>
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
<!--        <div class="tab-pane" id="profile_basic_info_tab">
            <?php //include 'profile_basic_info_tab_include.php'; ?>
        </div>-->
        <div class="tab-pane active" id="profile_dashboard_tab">
            <?php include 'profile_dashboard_tab_include.php'; ?>
        </div>
        <div class="tab-pane" id="profile_networks_tab">
            <?php include 'profile_networks_tab_include.php'; ?>
        </div>
        <div class="tab-pane" id="profile_events_tab">
            <?php include 'profile_events_tab_include.php'; ?>
        </div>
<!--        <div class="tab-pane" id="profile_inbox_tab">
            <?php //include 'profile_inbox_tab_include.php'; ?>
        </div>-->
        <div class="tab-pane" id="profile_accounts_tab">
            <?php include 'profile_accounts_tab_include.php'; ?>
        </div>
    </div>
</div>
<div class="clear"></div>
<script src="js/searchbar.js"></script>
<script src="js/file-input.js"></script>
