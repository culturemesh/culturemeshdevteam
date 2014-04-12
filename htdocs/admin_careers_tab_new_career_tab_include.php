<form class="form-horizontal" method="post" action="admin_upd.php" name="careers_form" id="careers_form" enctype="multipart/form-data">
    <input type="hidden" name="careers_tab" value="1" />
  <div class="control-group">
    <label class="control-label">Title</label>
    <div class="controls">
      <input type="text" id="job_title" name="job_title" placeholder="Job Title">
    </div>
  </div>
  <div class="control-group">
      <label class="control-label">Description</label>
    <div class="controls">
        <textarea id="job_desc" name="job_desc" placeholder="Job Description"></textarea>
    </div>
  </div>
    <div class="control-group">
    <div class="controls">
        <input type="submit" value="Add Job" class="btn cm-button"/>
    </div>
  </div>
</form>