<?php
class CMEmail
{
//-------------------------------------
////////////// THE EMAILS /////////////
private static $headers = <<<EOF
From: no-reply@culturemesh.com
MIME-Version: 1.0
Content-type: text/html; charset=iso-8859-1
EOF;
/////////////////////////////////////
// ----------------------------------

private static function getURI() {
	if(strpos($_SERVER['REQUEST_URI'], 'culturemeshdevteam') !== false) {
		return 'culturemeshdevteam/htdocs/';
	}
	else
	 { return ''; }
}
// sends a confirmation email to the address provided
public static function sendConfirmationEmail($address, $id, $act_code) {

	$uri = 'http://www.culturemesh.com/'.self::getURI().'confirmation.php?uid='.$id.'&act_code='.$act_code;
// --------------------------------------
// DEFINE EMAIL
//----------------------------------------
$confirmation = <<<EHTML
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Email Confirmation</title>
</head>
<body>
	<div>
		<h1>Almost Done!</h1>
		<p>Confirming your account will give you full
		access to CultureMesh, and all future notifications
		will be sent to this email address</p>

		<a href="$uri"
		>Click to confirm membership.</a>
	</div>
</body>
</html>
EHTML;
// ---------------------------------------
	return mail($address, 'CultureMesh confirmation', $confirmation, self::$headers);
// ---------------------------------------
} // end function

public static function sendChangePasswordEmail($email, $fp_code)
{
	$uri = 'http://www.culturemesh.com/'.self::getURI().'forgotpass.php?email='.$email.'&code='.$fp_code;

// DEFINE EMAIL
$cp_html = <<<EHTML
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Forgotten Password</title>
</head>
<body>
	<div>
		<h1>Almost There!</h1>
		<a href="$uri"
		>Click here to reset your password.</a>
	</div>
</body>
</html>
EHTML;
// ---------------------------------------
	return mail($email, 'Forgotten Password', $cp_html, self::$headers);
// ---------------------------------------
} // end function

public static function sendContactUsMsg($name, $address, $msg)
{
	$form = <<<EHTML
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Contact Us</title>
	</head>
	<body>
		<h1>Contact Us</h1>
		<div>
			<p>From: $name</p>
			<p>Email: $address</p>
		</div>
		<div>
			$msg
		</div>
	</body>
</html>
EHTML;
	// -------------------------
	return mail('ken@culturemesh.com', 'CultureMesh - Contact Us '.time() , $form, self::$headers);
	// -------------------------
} // end function
} // end class
?>
