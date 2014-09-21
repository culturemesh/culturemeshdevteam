<ul id="admin_careers_tabs" class="nav nav-pills">
    <li class="active"><a href="#new_career_entry" data-toggle="tab">New Career Entry</a></li>
    <li><a href="#careers_entry_admin" data-toggle="tab">Careers Admin</a></li>
</ul>
<div id="admin_careers_tabs_content" class="tab-content">
    <div class="tab-pane fade active in" id="new_career_entry">
        <?php
            include 'admin_careers_tab_new_career_tab_include.php';
        ?>
    </div>
    <div class="tab-pane fade" id="careers_entry_admin">
        <?php
            include 'admin_careers_tab_careers_admin_tab_include.php';
        ?>
    </div>
</div>