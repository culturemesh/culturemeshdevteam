<?php

// other php code

// environment
include '../../environment.php';
$cm = new \Environment();
$cm->displayErrors();

// error report
$response = array(
	'error' => NULL
	);
	
// activate mustache
$mustache = new misc\MustacheComponent();

$settings = NULL;
	
$email = new api\PostReplyEmail($cm, $mustache, 'inottage@yahoo.com, kenchester2@gmail.com', $settings);

if ($email->send() === True) {
	$response['error'] = 0;
	echo json_encode($response);
}
else {
	$response['error'] = 'Email not sent successfully';
	echo json_encode($response);
}

?>