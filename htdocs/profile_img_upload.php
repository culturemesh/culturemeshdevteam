<?php
// hackish
//define("UPLOAD_DIR", "/home3/culturp7/user_images/");
define("UPLOAD_DIR", "../../user_images/");

// if files aren't empty
if (!empty($_FILES['picfile'])) {
	// includes
	include_once 'data/dal_user.php';
	include_once 'zz341/fxn.php';

	include 'environment.php';
	$cm = new Environment();

	session_name($cm->session_name);
	session_start();

	$uid = $_SESSION['uid'];

	$file = $_FILES['picfile'];

	if ($file['error'] !== UPLOAD_ERR_OK) {
		echo json_encode(array(
			'success' => false,
			'error' => 'Sorry, there was a problem on the server'));
	}
	else {

		// check if it's an image file
		$fileType = getimagesize($file['tmp_name']);
//		$allowed_types = array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP);
		$allowed_types = array('image/png', 'image/jpeg', 'image/gif', 'image/bmp');
		//echo $fileType;
//		var_dump($fileType);
		//var_dump(IMAGETYPE_GIF);
		//echo ($fileType['mime']);
		if (!in_array($fileType['mime'], $allowed_types)) {
			echo json_encode(array(
				'success' => false,
				'error' => "Not the right image type. Must be png, jpeg, gif, or bmp."));
			
		}
		else {

			// check file name for safety
			//$name = preg_replace("/[^A-Z0-9._-]/i", "_", $file['name']);
			$name = 'pp.png';

			$con = getDBConnection();

			// check for existing user folder
			// if it don't exist, make it exist
			$cur_link = User::getImgLink($_POST['id'], $con);

			// user doesn't have directory
			if ($cur_link < 0) {
				// get timestamp
				list($msec, $sec) = explode(" ", microtime());

				// prepare directory
				$inc = 0;
				$rel_dir = $sec.'_';
				$dir = UPLOAD_DIR . $rel_dir.$inc.'/';

				//echo '</br>'.$dir;

				//echo 'user doesn\'t have directory';
				// check to see if directory already exists
				while (is_dir( $dir )) {
					//echo 'Not a directory';
					$inc++;
					$dir = UPLOAD_DIR . $rel_dir.$inc.'/';
				}

				// make directory
				mkdir( $dir, 0777, true );
				//echo "directory made";

				// set permissions
			//	chmod( $dir, 0644); 

				// permanentize reldir
				$rel_dir = $rel_dir.$inc.'/';
				//echo '</br>'.$rel_dir;

				// move to real home
				$success = move_uploaded_file($file['tmp_name'],
					$dir . $name);

				// store directory in database
				if (User::updateProfilePicture($rel_dir.$name, $_POST['id'], $con) == 1)
				{
					//echo "Successfully updated";
				}
				else {
					//echo "Successfully failed to update";
				}
			}
			else 
			{

				// mkdir in case we're on a different server
				if (!file_exists( UPLOAD_DIR . $cur_link )) {

					$path_no_ext = str_replace('pp.png', '', $cur_link);
					mkdir( UPLOAD_DIR . $path_no_ext);

					// move to real home
					$success = move_uploaded_file($file['tmp_name'],
						UPLOAD_DIR . $cur_link);
				}
				else {

					// unlink previous file
					if (file_exists(UPLOAD_DIR . $cur_link))
						unlink(UPLOAD_DIR . $cur_link);

					// move to real home
					$success = move_uploaded_file($file['tmp_name'],
						UPLOAD_DIR . $cur_link);

					// update last modified
					touch(UPLOAD_DIR . $cur_link);
				}
			}

			// set permissions on file
			//chmod($dir . $name, 0644);

			mysqli_close($con);

			// return to profile edit
			// AJAX
			if ($_POST['ajax'] == true) {
				$return_data = array(
					'success' => true,
					'error' => NULL);

				if (!$success) {
					$return_data['success'] = false;
					$return_data['error'] = "Had trouble storing the file, try again later.";
				}

				echo json_encode($return_data);

			}
			// NOJS
			else {
				if (!$success)
					header("Location: profile/$uid/?upload=fail");
				else
					header("Location: profile/$uid/");
			}
		}
	}
}
?>
