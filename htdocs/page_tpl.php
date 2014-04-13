<?php require_once('log.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php
	include 'headinclude.php';
        include $ppre.'_head.php';
	?>
</head>
<body>
    <div class="wrapper">
        <?php include 'header.php';?>
        <?php
            if(!isset($_SESSION['uid'])){
                include 'unauthorized_content.php';
            }
            else{
                include $ppre.'_body.php';
            }
	?>
	<?php include 'footer.php'; ?>
    </div>
</body>
</html>
