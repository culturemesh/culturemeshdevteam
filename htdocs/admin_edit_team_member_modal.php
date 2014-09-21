<?php
    $aet_header = 'Edit Team Member';
    $aet_body = '<form class="form-horizontal" method="post" action="admin_upd.php" name="aet_edit_form" id="aet_edit_form" enctype="multipart/form-data">
                    <input type="hidden" id="aet_edit_id" name="aet_edit_id"/>
                    <div class="control-group">
                    <label class="control-label">Name</label>
                    <div class="controls">
                      <input type="text" id="aet_edit_name" name="aet_edit_name" placeholder="Name">
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">Title</label>
                    <div class="controls">
                      <input type="text" id="aet_edit_title" name="aet_edit_title" placeholder="Job Title">
                    </div>
                  </div>
                  <div class="control-group">
                      <label class="control-label">Bio</label>
                    <div class="controls">
                        <textarea id="aet_edit_bio" name="aet_edit_bio" placeholder="Bio"></textarea>
                    </div>
                  </div>
                  <div class="control-group">
                      <label class="control-label">Thumbnail Image</label>
                    <div class="controls">
                      <img id="aet_edit_thumbnail" style="width: 258px; height: 166px;"/>
                      <input type="file" name="aet_edit_thumb" id="aet_edit_thumb" />
                    </div>
                  </div>
                    <div class="control-group">
                    <div class="controls">
                        <input type="submit" class="btn cm-button" value="Save Changes"/>
                    </div>
                  </div>
                </form>';
    $aet_footer = '';
    $aet_id = 'edit_team_member';
    echo buildModal($aet_header, $aet_body, $aet_footer, $aet_id);
?>