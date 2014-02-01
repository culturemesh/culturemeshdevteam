<?php
include_once 'html_builder.php';
include_once 'data/dal_user.php';
include_once 'data/dal_event.php';
include_once 'data/dal_event_registration.php';
include_once 'data/dal_network.php';
include_once 'data/dal_network_registration.php';
include_once 'data/dal_post.php';
include_once 'data/dal_conversation.php';

$con = getDBConnection();

$user = User::getUserById($_SESSION['uid'], $con);
?>
<div class="profile_left_panel">
    <span class="profile_image">
    	<img src="images/blank_profile_lrg.png"/>
    </span>
    </br>
    <span class="profile_name">
    	<h2 class="dashboard"><?php echo $user->first_name . " " . $user->last_name ; ?></h2>
    </span>
    <!--<a href="">Edit Profile</a>-->
</div>
<div class="profile_dashboard">
    <?php HTMLBuilder::displaySearchBar(); ?>
    <ul class="nav nav-pills dashboard">
        <li class="active"><a href="#profile_basic_info_tab" data-toggle="pill">Basic Info</a></li>
        <li><a href="#profile_dashboard_tab" data-toggle="pill">Dashboard</a></li>
        <li><a href="#profile_networks_tab" data-toggle="pill">Networks</a></li>
        <li><a href="#profile_events_tab" data-toggle="pill">Events</a></li>
        <li><a href="#profile_inbox_tab" data-toggle="pill">Inbox</a></li>
        <li><a href="#profile_accounts_tab" data-toggle="pill">Account</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="profile_basic_info_tab">
            <?php include 'profile_basic_info_tab_include.php'; ?>
        </div>
        <div class="tab-pane" id="profile_dashboard_tab">
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
<?php mysqli_close($con); ?>
