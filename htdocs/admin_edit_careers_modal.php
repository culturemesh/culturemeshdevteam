<?php
    $aec_header = 'Edit Career';
    $aec_body = '<form class="form-horizontal" method="post" action="admin_upd.php" name="aec_edit_form" id="aec_edit_form" enctype="multipart/form-data">
                    <input type="hidden" id="aec_edit_id" name="aec_edit_id"/>
                    <div class="control-group">
                    <label class="control-label">Job Title</label>
                    <div class="controls">
                      <input type="text" id="aec_edit_title" name="aec_edit_title" placeholder="Job Title">
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label">Description</label>
                    <div class="controls">
                      <input type="text" id="aec_edit_desc" name="aec_edit_desc" placeholder="Job Description">
                    </div>
                  </div>
                    <div class="control-group">
                    <div class="controls">
                        <input type="submit" class="btn cm-button" value="Save Changes"/>
                    </div>
                  </div>
                </form>';
    $aec_footer = '';
    $aec_id = 'edit_career';
    echo buildModal($aec_header, $aec_body, $aec_footer, $aec_id);
?>