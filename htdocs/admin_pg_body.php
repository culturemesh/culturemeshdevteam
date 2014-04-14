<ul id="admin_tabs" class="nav nav-tabs">
    <li class="active"><a href="#networks" data-toggle="tab">Networks</a></li>
    <li class=""><a href="#press" data-toggle="tab">Press</a></li>
    <li class=""><a href="#team" data-toggle="tab">Team</a></li>
    <li class=""><a href="#careers" data-toggle="tab">Careers</a></li>
</ul>
<div id="admin_tabs_content" class="tab-content">
    <div class="tab-pane fade" id="networks">
        <?php
            include 'admin_networks_tab_include.php';
        ?>
    </div>
    <div class="tab-pane fade active in" id="press">
        <?php
            include 'admin_press_tab_include.php';
        ?>
    </div>
    <div class="tab-pane fade" id="team">
        <?php
            include 'admin_team_tab_include.php';
        ?>
    </div>
    <div class="tab-pane fade active in" id="careers">
        <?php
            include 'admin_careers_tab_include.php';
        ?>
    </div>
</div>