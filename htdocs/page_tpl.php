<!DOCTYPE html>
<html lang="en">
<head>
	<?php
	include 'headinclude.php';
	?>
</head>
<body>
    <div class="wrapper">
        <?php include 'header.php';?>
        <?php
            if(!$_SESSION['uid']){
                include 'unauthorized_content.php';
            }
            else{
                include $ppre.'_body.php';
            }
	?>
    </div>
</body>
<?php
include 'footer.php';
?>
</html>