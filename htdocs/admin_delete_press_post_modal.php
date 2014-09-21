<?php
    $adp_header = 'Delete Press Post';
    $adp_body = '<form class="form-horizontal" method="post" action="admin_upd.php" name="adp_delete_form" id="adp_delete_form" enctype="multipart/form-data">
                    <input type="hidden" id="adp_delete_id" name="adp_delete_id"/>
                  <div class="control-group">
                    <label class="control-label">Are you sure you want to delete?</label>
                    <div class="controls">
                      <span id="adp_delete_title"></span>
                    </div>
                  </div>
                    <div class="control-group">
                    <div class="controls">
                        <input type="submit" class="btn cm-button" value="Delete Post"/>
                    </div>
                  </div>
                </form>';
    $adp_footer = '';
    $adp_id = 'delete_press_post';
    echo buildModal($adp_header, $adp_body, $adp_footer, $adp_id);
?>