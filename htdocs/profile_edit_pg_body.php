<div class="profile_left_panel">
    <span class="profile_image"></span>
    <span class="profile_name"></span>
    <a href="">Edit Profile</a>
</div>
<div class="profile_dashboard">
    <div class="profile_search_bar">
        <form method="get" action="search.php">
            <input type="text" name="query" placeholder="Search for types of people in locations" />
        </form>
    </div>
    <ul class="nav nav-pills">
        <li><a href="#profile_basic_info_tab" data-toggle="pill">Basic Info</a></li>
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
            db
            <?php //include 'profile_dashboard_tab_include.php'; ?>
        </div>
        <div class="tab-pane" id="profile_networks_tab">
            network
            <?php //include 'profile_networks_tab_include.php'; ?>
        </div>
        <div class="tab-pane" id="profile_events_tab">
            events
            <?php //include 'profile_events_tab_include.php'; ?>
        </div>
        <div class="tab-pane" id="profile_inbox_tab">
            <?php include 'profile_inbox_tab_include.php'; ?>
        </div>
        <div class="tab-pane" id="profile_accounts_tab">
            <?php include 'profile_accounts_tab_include.php'; ?>
        </div>
    </div>
</div>