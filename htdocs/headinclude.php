<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="author" content="<?php echo META_AUTHOR; ?>">
<link rel="icon" href="<?php echo DOMAIN_URL; ?>/favicon.ico" type="image/x-icon" />
<link rel="image_src" href="<?php echo DOMAIN_URL; ?>/logo.png" title="<?php echo DOMAIN_NAME;?>" id="<?php echo DOMAIN_NAME;?>" />
<noscript> <meta http-equiv=refresh content="0; URL=/noscript.php" /> </noscript>

<link href="<?php echo \Environment::$site_root; ?>css/bootstrap.css" rel="stylesheet">
<link href="<?php echo \Environment::$site_root; ?>css/style.css?<?php echo time(); ?>" rel="stylesheet">
<!--<link href="css/style.css" rel="stylesheet">-->
<!--[if lt IE 9]>
  <script src="js/html5shiv.js"></script>
<![endif]-->

<!--start secure(https) CDN items-->
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.js"></script>
<!--end CDN items-->

<!-- Facebook analytics stuff -->
<meta name="og:title" content="CultureMesh" />
<meta name="og:site_name" content="CultureMesh" />
<meta name="og:url" content="http://culturemesh.com"/>
<meta name="og:description" content="Connecting the World's Diasporas" />
<meta name="og:image" content="http://www.culturemesh.com/culturemesh-live/htdocs/images/CM_Logo_Final_square.jpg"/>

<script src="<?php echo \Environment::$site_root; ?>js/bootstrap.js"></script>
<script src="<?php echo \Environment::$site_root; ?>js/validation.js"></script>
<script src="<?php echo \Environment::$site_root; ?>js/account.js"></script>
<script src="<?php echo \Environment::$site_root; ?>js/fxn.js"></script>
<script src="<?php echo \Environment::$site_root; ?>js/ajax.js"></script>

<script type="text/javascript">
// register validation

// login validation
</script>
<link href='https://fonts.googleapis.com/css?family=Lato:300,400' rel='stylesheet' type='text/css'>

<?php
////////////////////////////////////////////////////////////////////
//
// 	MAKE HTTPS for non logged-in users

// check for localhost access
$whitelist = array("127.0.0.1", "::1");

if (!isset($_SESSION['uid']) && !in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
	if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "") {
		// redirect
		$redirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		header("Location: {$redirect}");
	}
}
?>
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
<script>
	/*
		* Goto function
	 */
	(function($) {
	    $.fn.goTo = function() {
		$('html, body').animate({
		    scrollTop: $(this).offset().top + 'px'
		}, 'fast');
		return this; // for chaining...
	    }
	})(jQuery);

	/*
		* Query string parser
	 */
	function QueryStringParser() {

		this.length = 0;
		this.qsGet = null;

		// get query string
		var qs = window.location.search.substring(1).split('?')[0];

		// split into array of key value pairs
		if (qs.length == 0) {
			this.length = 0;
		}
		else {
			// initialize qsGET
			this.qsGet = {};

			// split qs again in case of multiple params
			qs = qs.split('&');

			// for each  param, 
			// 	- split into key, value
			// 	- add to qsGET
			var param;
			for (var i = 0; i < qs.length; i++) {
				// split into two values
				param = qs[i].split('=');

				// add to object
				this.qsGet[param[0]] = decodeURIComponent(param[1]);
			}
		}
	}

	QueryStringParser.prototype.getQSObject = function() {
		return this.qsGet;
	}
</script>
<!--GoogleAn-->

<!--end googlan-->
