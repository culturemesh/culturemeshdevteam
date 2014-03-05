<script>
    $("#menu-about").addClass("active");
    </script>
<h2 class="pheader text-center">About CultureMesh</h2>
<p class="text-center">CultureMesh is building networks to match the real-world dynamics of people living, working, and traveling outside of their home countries to knit the diverse fabrics of our world together.</p>

<div class="mid-grid">

</div>
<div class="mid-grid">
    <h2 class="pheader text-center">The Team</h2>
    <ul class="img-strip-ul center-elem">
        <?php
        $team_members = getRowsQuery("SELECT * FROM internal_team");
        foreach($team_members as $member):?>
            <li><img src="<?php echo $member['thumb_url'];?>" title="<?php echo $member['name'];?>" alt="<?php echo $member['name'];?>" class="team_thumb" /></li>
        <?php endforeach;?>
    </ul>
</div>

<div class="bottom-info">
    <h2 class="pheader text-center">Contact Us</h2>
    <?php
        if($_POST['contact_name'] && $_POST['contact_body']){
            //$msg = $_POST['contact_name']."(".$_POST['contact_email'].") said: \r\n".$_POST['contact_body'];
            //sendEmailNotification(CONTACT_EMAIL, "Website Contact Form Submitted", $msg);
            echo '<span class="label label-success">Thanks! Our team is looking forward to reading what you had to say!</span>';
        }
    ?>
    <form method="post" action="" class="center-elem fullwidth">
        <input placeholder="Name" type="text" required name="name" class="input-half left" id="contact_name">
        <input placeholder="Your Email" type="email" class="input-half right" id="contact_email">
        <textarea class="center-elem full" placeholder="What do you have to say?" name="contact_body" id="contact_body" required></textarea>
        <input type="submit" value="Send Message" class="btn cm-button center-elem">
    </form>
</div>