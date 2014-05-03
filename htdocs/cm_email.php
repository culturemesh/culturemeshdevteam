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

private static $confirmation = <<<EHTML
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

		<a href="http://www.culturemesh.com/culturemeshdevteam/
		htdocs/confirmation.php"
		>Click to confirm membership.</a>
	</div>
</body>
</html>
EHTML;
/////////////////////////////////////
// ----------------------------------

	// sends a confirmation email to the address provided
	public static function sendConfirmationEmail($address)
	{
		return mail($address, 'CultureMesh confirmation', self::$confirmation, self::$headers);
	}
}
?>
