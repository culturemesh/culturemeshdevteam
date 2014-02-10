<ul id="admin_team_tabs" class="nav nav-pills">
    <li class="active"><a href="#new_team_entry" data-toggle="tab">New Team Member Entry</a></li>
    <li class=""><a href="#team_entry_admin" data-toggle="tab">Team Member Admin</a></li>
</ul>
<div id="admin_team_tabs_content" class="tab-content">
    <div class="tab-pane fade" id="new_team_entry">
        <?php
            include 'admin_team_tab_new_team_tab_include.php';
        ?>
    </div>
    <div class="tab-pane fade active in" id="team_entry_admin">
        <?php
            include 'admin_team_tab_team_admin_tab_include.php';
        ?>
    </div>
</div>