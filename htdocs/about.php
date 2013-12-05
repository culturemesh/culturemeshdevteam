<?php
require_once 'log.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php
	include 'headinclude.php';
	?>
	<title>About - <?php echo DOMAIN_NAME; ?></title>
	<meta name="keywords" content="" />
	<meta name="description" content="Get your start-up up & running with Kos to Coast Development! If your startup is understaffed, we'll step in and give you a hand with software development." />
</head>
<body id="careers" class="info-pg">
    <div class="wrapper">
        <?php
        include 'header.php';
        ?>
        <script>
            $("#menu-about").addClass("active");
            </script>
        <h2>About CultureMesh</h2>
        <p>CultureMesh is building networks to match he real-world dynamics of people living, working, and traveling outside of their home countries to knit the diverse fabrics of our world together.</p>
        <a href="" class="btn">See Current Openings</a>
        
        <div class="mid-grid">
            
        </div>
        <div class="mid-grid">
            <h2>The Team</h2>
            <ul>
                <li><img src="sd.png" class="team_thumb"></li>
                <li><img src="df.ong" class="team_thumb"></li>
                <li><img src="fd" class="team_thumb"></li>
                <li><img src="fdfd" class="team_thumb"></li>
                <li><img src="df" class="team_thumb"></li>
            </ul>
        </div>
        
        <div class="bottom-info">
            <h2>Contact Us</h2>
            <?php
                if($_POST['contact_name'] && $_POST['contact_body']){
                    //$msg = $_POST['contact_name']."(".$_POST['contact_email'].") said: \r\n".$_POST['contact_body'];
                    //sendEmailNotification(CONTACT_EMAIL, "Website Contact Form Submitted", $msg);
                    echo '<span class="label label-success">Thanks! Our team is looking forward to reading what you had to say!</span>';
                }
            ?>
            <form method="post" action="">
                <input placeholder="Name" type="text" required name="name" id="contact_name">
                <input placeholder="Your Email" type="email" id="contact_email">
                <textarea name="contact_body" id="contact_body" required></textarea>
                <input type="submit" value="Send Message" class="btn">
            </form>
        </div>
    </div>
</body>
<?php
include 'footer.php';
?>
</html>