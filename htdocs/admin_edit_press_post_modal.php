<?php
    $aep_header = 'Edit Press Post';
    $aep_body = '<form class="form-horizontal" method="post" action="admin_upd.php" name="aep_edit_form" id="aep_edit_form" enctype="multipart/form-data">
                    <input type="hidden" id="aep_edit_id" name="aep_edit_id"/>
                  <div class="control-group">
                    <label class="control-label">Title</label>
                    <div class="controls">
                      <input type="text" id="aep_edit_title" name="aep_edit_title" placeholder="Title">
                    </div>
                  </div>
                  <div class="control-group">
                    <label class="control-label" for="inputTitle">Sub-Title</label>
                    <div class="controls">
                      <input type="text" id="aep_edit_subtitle" name="aep_edit_subtitle" placeholder="Sub-Title">
                    </div>
                  </div>
                  <div class="control-group">
                      <label class="control-label">Body</label>
                    <div class="controls">
                        <textarea id="aep_edit_body" name="aep_edit_body" placeholder="Body"></textarea>
                    </div>
                  </div>
                  <div class="control-group">
                      <label class="control-label">Thumbnail Image</label>
                    <div class="controls">
                      <img id="aep_edit_thumbnail" style="width: 258px; height: 166px;"/>
                      <input type="file" name="aep_edit_thumb" id="aep_edit_thumb" />
                    </div>
                  </div>
                    <div class="control-group">
                    <div class="controls">
                        <input type="submit" class="btn cm-button" value="Save Changes"/>
                    </div>
                  </div>
                </form>';
    $aep_footer = '';
    $aep_id = 'edit_press_post';
    echo buildModal($aep_header, $aep_body, $aep_footer, $aep_id);
?>