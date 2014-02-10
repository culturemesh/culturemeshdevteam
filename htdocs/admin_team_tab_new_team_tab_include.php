<form class="form-horizontal" method="post" action="admin_upd.php" name="team_form" id="team_form" enctype="multipart/form-data">
    <input type="hidden" name="team_tab" value="1" />
  <div class="control-group">
    <label class="control-label">Name</label>
    <div class="controls">
      <input type="text" id="member_name" name="member_name" placeholder="Name">
    </div>
  </div>
  <div class="control-group">
    <label class="control-label">Title</label>
    <div class="controls">
      <input type="text" id="member_title" name="member_title" placeholder="Title">
    </div>
  </div>
  <div class="control-group">
      <label class="control-label">Bio</label>
    <div class="controls">
        <textarea id="member_bio" name="member_bio" placeholder="Bio"></textarea>
    </div>
  </div>
  <div class="control-group">
      <label class="control-label">Thumbnail Image</label>
    <div class="controls">
      <input type="file" name="member_thumb" id="member_thumb" />
    </div>
  </div>
    <div class="control-group">
    <div class="controls">
        <input type="submit" value="Add Member" class="btn cm-button"/>
    </div>
  </div>
</form>