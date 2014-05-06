<?php
// hackish
define("UPLOAD_DIR", "/home3/culturp7/user_images/");

if (!empty($_FILES['picfile'])) {
	$file = $_FILES['picfile'];

	if ($file['error'] !== UPLOAD_ERR_OK) {
		exit;
	}

	// check file name for safety
	$name = preg_replace("/[^A-Z0-9._-]/i", "_", $file['name'];

	// check for existing file
	//
	// move to real home
	$success = move_uploaded_file($file['tmp_name'],
		UPLOAD_DIR . $name);

	if (!$success) {
		echo "<p>Unable to save file</p>";
		exit;

	// set permissions on file
	chmod(UPLOAD_DIR . $name, 0644);
?>
