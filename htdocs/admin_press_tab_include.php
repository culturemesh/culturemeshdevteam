<ul id="admin_press_tabs" class="nav nav-pills">
    <li class="active"><a href="#new_press_entry" data-toggle="tab">New Press Entry</a></li>
    <li class=""><a href="#press_entry_admin" data-toggle="tab">Entry Admin</a></li>
</ul>
<div id="admin_press_tabs_content" class="tab-content">
    <div class="tab-pane fade" id="new_press_entry">
        <?php
            include 'admin_press_tab_new_press_tab_include.php';
        ?>
    </div>
    <div class="tab-pane fade active in" id="press_entry_admin">
        <?php
            include 'admin_press_tab_entry_admin_tab_include.php';
        ?>
    </div>
</div>