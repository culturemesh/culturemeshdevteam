<?php
require_once('log.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php
	include 'headinclude.php';
	?>
	<title>Careers - <?php echo DOMAIN_NAME; ?></title>
	<meta name="keywords" content="" />
	<meta name="description" content="Get your start-up up & running with Kos to Coast Development! If your startup is understaffed, we'll step in and give you a hand with software development." />
        
        <script src="http://www.google.com/jsapi" type="text/javascript"></script>
        <script type="text/javascript">google.load("jquery","1.6.1");</script>
        <link rel="stylesheet" href="css/jsquares.css" type="text/css" media="all" />
        <script src="js/jquery.hoverintent.min.js" type="text/javascript"></script>
	<script src="js/jquery.jsquares.js" type="text/javascript"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$('#js-container').jsquares();
		});
	</script>
</head>
<body id="careers" class="info-pg">
    <div class="container">
      <form class="form-signin" method="post" action="?action=login">
        <h2 class="form-signin-heading">Please sign in</h2>
        <input type="email" class="input-block-level" name="email" placeholder="Email Address" required>
        <input type="password" class="input-block-level" name="password" placeholder="Password" required>
        <label class="checkbox">
          <input type="checkbox" value="remember-me"> Remember me
        </label>
        <label><a href="#fp_modal" data-toggle="modal">Forgot Password</a></label>
        <button class="btn btn-large cm-button" type="submit">Sign in</button>
      </form>
    </div> <!-- /container -->
</body>
<?php
include 'footer.php';
?>
</html>