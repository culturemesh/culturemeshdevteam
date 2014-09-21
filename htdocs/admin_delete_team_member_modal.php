<?php
    $adt_header = 'Delete Team Member';
    $adt_body = '<form class="form-horizontal" method="post" action="admin_upd.php" name="adt_delete_form" id="adt_delete_form" enctype="multipart/form-data">
                    <input type="hidden" id="adt_delete_id" name="adt_delete_id"/>
                  <div class="control-group">
                    <label class="control-label">Are you sure you want to delete?</label>
                    <div class="controls">
                      <span id="adt_delete_name"></span>
                    </div>
                  </div>
                    <div class="control-group">
                    <div class="controls">
                        <input type="submit" class="btn cm-button" value="Delete Team Member"/>
                    </div>
                  </div>
                </form>';
    $adt_footer = '';
    $adt_id = 'delete_team_member';
    echo buildModal($adt_header, $adt_body, $adt_footer, $adt_id);
?>