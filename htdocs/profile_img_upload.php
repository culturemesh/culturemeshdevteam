<?php
ini_set("display_errors", true);

// hackish
define("UPLOAD_DIR", "/home3/culturp7/user_images/");
//define("UPLOAD_DIR", "../../../user_images/");
if (!empty($_FILES['picfile'])) {

	// includes
	include_once 'data/dal_user.php';
	include_once 'zz341/fxn.php';

	echo 'here';
	$file = $_FILES['picfile'];

	if ($file['error'] !== UPLOAD_ERR_OK) {
		exit;
	}

	/*
	// check if it's an image file
	$fileType = getimagesize($file['tmp_name']);
	$allowed_types = array(IMAGETYPE_GIF, IMAGETYPE_JPG, IMAGETYPE_PNG);
	if (!in_array($fileType, $allowed_types))
		exit;
	*/

	// check file name for safety
	$name = preg_replace("/[^A-Z0-9._-]/i", "_", $file['name']);
	//$name = 'pp';

	$inc = 0;
	$rel_dir = microtime().'_'.$inc.'/';
	$dir = UPLOAD_DIR . $rel_dir;

	$con = getDBConnection();

	// check for existing user folder
	// if it don't exist, make it exist
	(!is_dir( $dir )) {
		echo 'Not a directory';
		mkdir( $dir );
		// set permissions
	//	chmod( $dir, 0644); 
	}

	// move to real home
	$success = move_uploaded_file($file['tmp_name'],
		$dir . $name);

	if (!$success) {
		echo "<p>Unable to save file</p>";
		exit;
	}

	// set permissions on file
	//chmod($dir . $name, 0644);

	if (User::updateProfilePicture($rel_dir.$name, $_POST['id'], $con) == 1)
	{
		echo "Successfully updated";
	}
	else
		echo "Successfully failed to update";
}
?>
