<style>
    .press_subtitle{
        font: 20px Lato;
        color: #333;
        text-transform: none;
    }
    .press_body{
        font: 18px Lato;
        font-weight: 200;
        color: #000;
        
    }
</style>
<h2>Recent Press</h2>
<div class="press">
    <?php
    //may need these ids for css purposes..
    //id="careers" class="info-pg"
    //TODO:MAKE QUERY FOR MEDIA, THEN LOOP
    $press_posts = getRowsQuery("SELECT * FROM internal_press ORDER BY date DESC");
    foreach($press_posts as $post):
        $thumb = ($post['thumb_url'] != NULL) ? "images/" . $post['thumb_url'] : "images/CM_Logo_Final_square.jpg";//need default img
    ?>
    <div class="media">
        <a class="pull-left" href="#">
          <img class="media-object" data-src="<?php echo $thumb; ?>" alt="64x64" style="width: 258px; height: 166px;" src="<?php echo $thumb;?>">
        </a>
        <div class="media-body">
	  <h4 class="media-heading"><a target="_blank" href="<?php echo $post['url'] ?>"><?php echo $post['title'];?></a></h4>
          <h5 class="media-heading press_subtitle"><?php echo $post['sub_title'];?></h5>
          <div class="press_body">
          <?php echo $post['body'];?>
          </div>
        </div>
      </div>
    <?php endforeach;?>
</div>
