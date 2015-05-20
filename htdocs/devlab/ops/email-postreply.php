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

// dummy reply
$reply = new dobj\Reply();
$reply->first_name = 'Blanky';
$reply->last_name = 'McBlank';
$reply->reply_text = 'Lorem ipsum dui mauris velit curabitur sagittis consequat felis metus iaculis, eros ut sit nec quisque mi faucibus adipiscing fermentum iaculis consequat purus ultricies potenti adipiscing ornare aliquam, vitae feugiat lectus pretium himenaeos congue.';

$settings = array(
	'reply' => $reply->prepare($cm)
);
	
$email = new api\PostReplyEmail($cm, $mustache, 'inottage@yahoo.com', $settings);
$message = $email->getMessage();
$file_from_root = $cm->ds . 'devlab' . $cm->ds . 'misc' . $cm->ds . 'post-reply-email.html';
$filename = \Environment::$site_root . $file_from_root;
$filename_host = $cm->host_root . $file_from_root;
$file = fopen($filename, "w") or die("Unable to open file!");
fwrite($file, $message);
fclose($file);

$response['error'] = 0;
$response['link'] = $filename_host;
echo json_encode($response);

?>
