<form class="form-horizontal" method="post" action="admin_upd.php" name="press_form" id="press_form" enctype="multipart/form-data">
    <input type="hidden" name="press_tab" value="1" />
  <div class="control-group">
    <label class="control-label">Title</label>
    <div class="controls">
      <input type="text" id="press_title" name="press_title" placeholder="Title">
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="inputTitle">Sub-Title</label>
    <div class="controls">
      <input type="text" id="press_subtitle" name="press_subtitle" placeholder="Title">
    </div>
  </div>
  <div class="control-group">
      <label class="control-label">Body</label>
    <div class="controls">
        <textarea id="press_body" name="press_body" placeholder="Body"></textarea>
    </div>
  </div>
  <div class="control-group">
      <label class="control-label">Thumbnail Image</label>
    <div class="controls">
      <input type="file" name="press_thumb" id="press_thumb" />
    </div>
  </div>
    <div class="control-group">
    <div class="controls">
        <input type="submit" value="Publish" class="btn cm-button"/>
    </div>
  </div>
</form>