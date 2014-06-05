<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="author" content="<?php echo META_AUTHOR; ?>">
<link rel="icon" href="<?php echo DOMAIN_URL; ?>/favicon.ico" type="image/x-icon" />
<link rel="image_src" href="<?php echo DOMAIN_URL; ?>/logo.png" title="<?php echo DOMAIN_NAME;?>" id="<?php echo DOMAIN_NAME;?>" />
<noscript> <meta http-equiv=refresh content="0; URL=/noscript.php" /> </noscript>

<link href="<?php echo HOME_PATH; ?>css/bootstrap.css" rel="stylesheet">
<link href="<?php echo HOME_PATH; ?>css/style.css?<?php echo time(); ?>" rel="stylesheet">
<!--<link href="css/style.css" rel="stylesheet">-->
<!--[if lt IE 9]>
  <script src="js/html5shiv.js"></script>
<![endif]-->

<!--start secure(https) CDN items-->
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.js"></script>
<!--end CDN items-->


<script src="<?php echo HOME_PATH; ?>js/bootstrap.js"></script>
<script src="<?php echo HOME_PATH; ?>js/validation.js"></script>
<script src="<?php echo HOME_PATH; ?>js/account.js"></script>
<script src="<?php echo HOME_PATH; ?>js/fxn.js"></script>
<script src="<?php echo HOME_PATH; ?>js/ajax.js"></script>

<script type="text/javascript">
// register validation

// login validation
</script>
<link href='http://fonts.googleapis.com/css?family=Lato:300,400' rel='stylesheet' type='text/css'>

<?php
	$guest = true;
	if (!isset($_SESSION['uid']))
		$guest = true;
	else
	{
		$guest = false;
		$user = User::getUserById($_SESSION['uid'], $con);
		$user_email = $user->email;
	}
?>
<style type='text/css'>
	<?php if (isset($_SESSION['uid'])) : ?>
		#login-link {
		    display:none;
		}
		
		#register-link {
		    display:none;
		}
		
		.guest {
		    display:none;
		}
	<?php else : ?>
		#welcome {
		    display: none;
		}
		
		#sign-out {
		    display: none;
		}
		
	<?php endif; ?>
</style>
<!--GoogleAn-->

<!--end googlan-->
