<?php include 'admin_edit_team_member_modal.php'; ?>
<?php include 'admin_delete_team_member_modal.php'; ?>

<table class="table">
    <tr>
        <th>Name</th>
        <th>Title</th>
        <th>Bio</th>
        <th>Thumb</th>
        <th>Member Since</th>
        <th>Actions</th>
    </tr>
    <?php
    //may need these ids for css purposes..
    //id="careers" class="info-pg"
    //TODO:MAKE QUERY FOR MEDIA, THEN LOOP
    $team_members = getRowsQuery("SELECT * FROM internal_team ORDER BY team_member_since DESC");
    foreach($team_members as $tin=> $member):
    ?>
    <tr>
        <td><?php echo $member['name'];?></td>
        <td><?php echo $member['job_title'];?></td>
        <td><?php echo $member['bio'];?></td>
        <td><img src="<?php echo $member['thumb_url'];?>" style="width: 258px; height: 166px;"/></td>
        <td><?php echo date('F j, Y', $member['team_member_since']);?></td>
        <td>
            <a class="btn btn-inverse" data-toggle="modal" id="edit_team_member_btn<?php echo $tin;?>" href="#edit_team_member" title="Edit Team Member"><i class="icon icon-edit icon-white"></i></a>
            <script>
                $("#edit_team_member_btn<?php echo $tin;?>").click(function(){
                    $("#aet_edit_name").val("<?php echo $member['name'];?>");
                    $("#aet_edit_id").val("<?php echo $member['id'];?>");
                    $("#aet_edit_title").val("<?php echo $member['job_title'];?>");
                    $("#aet_edit_bio").val("<?php echo $member['bio'];?>");
                    $("#aet_edit_thumbnail").attr("src","<?php echo $member['thumb_url'];?>");
                });
            </script>
            <a class="btn btn-danger" data-toggle="modal" id="delete_team_member_btn<?php echo $tin;?>" href="#delete_team_member" title="Delete Team Member"><i class="icon icon-remove icon-white"></i></a>
            <script>
                $("#delete_team_member_btn<?php echo $tin;?>").click(function(){
                    $("#adt_delete_id").val("<?php echo $member['id'];?>");
                    $("#adt_delete_name").html("<?php echo $member['name'];?>");
                });
            </script>
        </td>
    </tr>
    <?php endforeach; ?>
</table>