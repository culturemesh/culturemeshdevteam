<?php
    $adc_header = 'Delete Career';
    $adc_body = '<form class="form-horizontal" method="post" action="admin_upd.php" name="adc_delete_form" id="adc_delete_form">
                    <input type="hidden" id="adc_delete_id" name="adc_delete_id"/>
                  <div class="control-group">
                    <label class="control-label">Are you sure you want to delete?</label>
                    <div class="controls">
                      <span id="adc_delete_title"></span>
                    </div>
                  </div>
                    <div class="control-group">
                    <div class="controls">
                        <input type="submit" class="btn cm-button" value="Delete Career"/>
                    </div>
                  </div>
                </form>';
    $adc_footer = '';
    $adc_id = 'delete_career';
    echo buildModal($adc_header, $adc_body, $adc_footer, $adc_id);
?>