<?php 
	//include_once('log.php'); 
	include_once('data/dal_user.php');
	$con = getDBConnection();
?>
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
        <?php 
        include 'header.php';
        include $ppre.'_body.php';
	include 'footer.php';
	?>
    </div>
</body>
</html>
