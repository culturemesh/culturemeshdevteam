<?php include 'admin_edit_press_post_modal.php'; ?>
<?php include 'admin_delete_press_post_modal.php'; ?>

<table class="table">
    <tr>
        <th>Title</th>
        <th>Sub-Title</th>
        <th>Body</th>
        <th>Thumb</th>
        <th>Date</th>
        <th>Actions</th>
    </tr>
    <?php
    //may need these ids for css purposes..
    //id="careers" class="info-pg"
    //TODO:MAKE QUERY FOR MEDIA, THEN LOOP
    $press_posts = getRowsQuery("SELECT * FROM internal_press ORDER BY date DESC");
    foreach($press_posts as $pin=> $post):
    ?>
    <tr>
        <td><?php echo $post['title'];?></td>
        <td><?php echo $post['sub_title'];?></td>
        <td><?php echo $post['body'];?></td>
        <td><img src="<?php echo $post['thumb_url'];?>" style="width: 258px; height: 166px;"/></td>
        <td><?php echo date('F j, Y', $post['date']);?></td>
        <td>
            <a class="btn btn-inverse" data-toggle="modal" id="edit_press_post_btn<?php echo $pin;?>" href="#edit_press_post" title="Edit Post"><i class="icon icon-edit icon-white"></i></a>
            <script>
                $("#edit_press_post_btn<?php echo $pin;?>").click(function(){
                    $("#aep_edit_id").val("<?php echo $post['id'];?>");
                    $("#aep_edit_title").val("<?php echo $post['title'];?>");
                    $("#aep_edit_subtitle").val("<?php echo $post['sub_title'];?>");
                    $("#aep_edit_body").val("<?php echo $post['body'];?>");
                    $("#aep_edit_thumbnail").attr("src","<?php echo $post['thumb_url'];?>");
                });
            </script>
            <a class="btn btn-danger" data-toggle="modal" id="delete_press_post_btn<?php echo $pin;?>" href="#delete_press_post" title="Delete Post"><i class="icon icon-remove icon-white"></i></a>
            <script>
                $("#delete_press_post_btn<?php echo $pin;?>").click(function(){
                    $("#adp_delete_id").val("<?php echo $post['id'];?>");
                    $("#adp_delete_title").html("<?php echo $post['title'];?>");
                });
            </script>
        </td>
    </tr>
    <?php endforeach; ?>
</table>