<?php
ini_set('display_errors', true);
include('../Environment.php');
$cm = new Environment();

$storage = new \Upload\Storage\FileSystem(Environment::$site_root.'/devlab/images', true);
$file = new \Upload\File('fileupload', $storage);

// Validate file upload
// MimeType List => http://www.webmaster-toolkit.com/mime-types.shtml
$file->addValidations(array(
    // Ensure file is of type "image/png"
    //new \Upload\Validation\Mimetype('image/png'),

    //You can also add multi mimetype validation
    new \Upload\Validation\Mimetype(array('image/png', 'image/gif', 'image/jpeg')),

    // Ensure file is no larger than 5M (use "B", "K", M", or "G")
    new \Upload\Validation\Size('2M')
));

$data = array(
    'name'       => $file->getNameWithExtension(),
    'extension'  => $file->getExtension(),
    'mime'       => $file->getMimetype(),
    'size'       => $file->getSize(),
    'md5'        => $file->getMd5(),
    'dimensions' => $file->getDimensions()
);

// Try to upload file
try {
	// Success!
	$file->upload();

	// make thumbnails
	//

	echo json_encode(array(
		'success' => true,
		'file-count' => count($_FILES['fileupload']['name']),
	));
} catch (\Exception $e) {
	// Fail
	$errors = $file->getErrors();
	print_r($errors);
}


?>
