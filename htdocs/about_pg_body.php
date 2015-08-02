<script>
    $("#menu-about").addClass("active");
    </script>
<h2 class="pheader text-center">About CultureMesh</h2>
<p class="text-center">CultureMesh is building networks to match the real-world dynamics of people living, working, and traveling outside of their home countries to knit the diverse fabrics of our world together.</p>

<div class="mid-grid">

</div>
<div id="team" class="mid-grid">
    <h2 class="pheader text-center">The Team</h2>
    <div>
	    <?php
		$pic_length = 205;
		$team_members = getRowsQuery("SELECT * FROM internal_team");
		$team_members_count = count($team_members);
		$row_length = 4;

		$ul_count = $team_members_count / 4;
		$item_width = $pic_length + 6;
		
	    ?>
		<?php for($i = 0; $i < $team_members_count;):?>
			<?php
			$quotient = $i / $row_length;

			if ($quotient === 0) {
				$row_item_count = $row_length;
			}
			else {
				$row_item_count = $team_members_count - $i;
			}	

			$row_width = $row_item_count * $item_width;
			?>
			<div id="team-row-container">
			<ul class="img-strip-ul team" style="width:<?php echo $row_width;?>px">
			<?php for($j = 0; $j < $row_length && $i < $team_members_count; $j++, $i++):?>
				<li class="team-member">
					<div class="team-member">
					<img src="<?php echo $team_members[$i]['thumb_url'];?>" title="<?php echo $team_members[$i]['name'];?>" alt="<?php echo $team_members[$i]['name'];?>" class="team_thumb" /></br>
					<p class="team team-name"><?php echo $team_members[$i]['name']; ?></p>
					<p class="team team-job-title"><?php echo $team_members[$i]['job_title']; ?></p>
					</div>
				</li>
			<?php endfor;?>
	    		</ul>
			</div>
		<?php endfor; ?>
	    <div class="clear"></div>
    </div>
</div>

<div class="bottom-info">
    <h2 class="pheader text-center">Contact Us</h2>
    <?php
        if(isset($_POST['contact_name']) && ($_POST['contact_body'])
		&& isset($_POST['contact_email'])){
		if (CMEmail::sendContactUsMsg($_POST['contact_name'], $_POST['contact_email'], $_POST['contact_body']))
            		echo '<span class="label label-success">Thanks! Our team is looking forward to reading what you had to say!</span>';
		else echo '<span class="label label-success">Sorry, your email didn\'t get sent. Try again later. We want to hear what you have to say!</span>';
        }
	else {
		//echo '<span class="label label-success">Please fill out all fields.</span>';
	}
    ?>
    <form method="post" action="" class="center-elem fullwidth">
	<div style="float:left;">
        <input placeholder="Name" type="text" required name="contact_name" class="input-half left" id="contact_name">
        <input placeholder="Your Email" type="email" class="input-half right" id="contact_email" name="contact_email">
	</div>
	<div class="clear"></div>
        <textarea class="center-elem full" placeholder="What do you have to say?" name="contact_body" id="contact_body" required></textarea>
        <input type="submit" value="Send Message" class="btn cm-button center-elem">
    </form>
</div>
