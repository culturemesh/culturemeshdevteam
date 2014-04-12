<?php include 'admin_edit_careers_modal.php'; ?>
<?php include 'admin_delete_careers_modal.php'; ?>

<table class="table">
    <tr>
        <th>Title</th>
        <th>Description</th>
        <th>Date Posted</th>
        <th>Actions</th>
    </tr>
    <?php
    //may need these ids for css purposes..
    //id="careers" class="info-pg"
    //TODO:MAKE QUERY FOR MEDIA, THEN LOOP
    $careers = getRowsQuery("SELECT * FROM internal_careers");
    foreach($careers as $cin=> $career):
    ?>
    <tr>
        <td><?php echo $career['job_title'];?></td>
        <td><?php echo $career['job_description'];?></td>
        <td><?php echo date('F j, Y', $career['date_posted']);?></td>
        <td>
            <a class="btn btn-inverse" data-toggle="modal" id="edit_careers_btn<?php echo $cin;?>" href="#edit_career" title="Edit Career"><i class="icon icon-edit icon-white"></i></a>
            <script>
                $("#edit_careers_btn<?php echo $cin;?>").click(function(){
                    $("#aec_edit_title").val("<?php echo $career['job_title'];?>");
                    $("#aec_edit_id").val("<?php echo $career['id'];?>");
                    $("#aec_edit_desc").val("<?php echo $career['job_description'];?>");
                });
            </script>
            <a class="btn btn-danger" data-toggle="modal" id="delete_career_btn<?php echo $cin;?>" href="#delete_career" title="Delete Career"><i class="icon icon-remove icon-white"></i></a>
            <script>
                $("#delete_career_btn<?php echo $cin;?>").click(function(){
                    $("#adc_delete_id").val("<?php echo $career['id'];?>");
                    $("#adc_delete_title").html("<?php echo $career['job_title'];?>");
                });
            </script>
        </td>
    </tr>
    <?php endforeach; ?>
</table>