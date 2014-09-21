<?php
ini_set('display_errors', true);

include "cm_email.php"; 

echo "Class Syntax ok</br>";

if (CMEmail::sendConfirmationEmail('inottage@yahoo.com'))
	echo "Email sent";
else
	echo "Solly, holmes. No dice.";

?>
