<?php
//ini_set('display_errors', true);
include('../Environment.php');
$cm = new Environment();

$img_dir = Environment::$site_root.'/devlab/images';
$img_dir_host = $cm->host_root.'/devlab/images';

$iu = new \misc\ImageUpload($cm, array(
		'dir' => $cm->img_repo_dir,
		'postname' => 'fileupload',
		'validation_type' => array('image/png', 'image/gif', 'image/jpeg'),
		'validation_size' => '2M',
		'thumbnail' => array(
			'thumbnail' => true,
			'class' => 'post')
		)
	);

$result = $iu->upload();

if (!isset($result['files'])) {

}

$files = $result['files'];

// create db connnection
$dal = new dal\DAL($cm->getConnection());
$dal->loadFiles();
$do2db = new dal\Do2Db();

foreach ($files as $file) {
	$file->insert($dal, $do2db);
}

$cm->closeConnection();
/*
// try and create thumbnails out of 'em
$file_image = new Imagick($img_dir.'/'.$data['name']);
$file_image->thumbnailImage(75, 0);
$file_image->writeImage($img_dir.'/'.'new_name.png');
 */

/*
var_dump($file_image);
$thumb = clone $file_image;

$new_name = $img_dir.'/'.'thumb.'.$data['extension'];
$new_name_host = $img_dir_host.'/'.'thumb.'.$data['extension'];
$thumb->setFilename($new_name);
$thumb->thumbnailImage(75, 0);
$thumb->writeImage();
 */

//print_r($result['files']);
/*
$storage = new \Upload\Storage\FileSystem(Environment::$site_root.'/devlab/images', true);
$files = new \Upload\File('fileupload', $storage);

// Validate file upload
// MimeType List => http://www.webmaster-toolkit.com/mime-types.shtml
$files->addValidations(array(
    // Ensure file is of type "image/png"
    //new \Upload\Validation\Mimetype('image/png'),

    //You can also add multi mimetype validation
    new \Upload\Validation\Mimetype(array('image/png', 'image/gif', 'image/jpeg')),

    // Ensure file is no larger than 5M (use "B", "K", M", or "G")
    new \Upload\Validation\Size('2M')
));


$data = array(
    'name'       => $files[0]->getNameWithExtension(),
    'extension'  => $files[0]->getExtension(),
    'mime'       => $files[0]->getMimetype(),
    'size'       => $files[0]->getSize(),
    'md5'        => $files[0]->getMd5(),
    'dimensions' => $files[0]->getDimensions()
);


foreach ($files as $file) {
	
}

$files->upload();

// try and create thumbnails out of 'em
$file_image = new Imagick($img_dir.'/'.$data['name']);
$file_image->thumbnailImage(75, 0);
$file_image->writeImage($img_dir.'/'.'new_name.png');
 */
/*
var_dump($file_image);
$thumb = clone $file_image;

$new_name = $img_dir.'/'.'thumb.'.$data['extension'];
$new_name_host = $img_dir_host.'/'.'thumb.'.$data['extension'];
$thumb->setFilename($new_name);
$thumb->thumbnailImage(75, 0);
$thumb->writeImage();
 */

echo json_encode(array(
	'success' => true,
	'file-count' => count($_FILES['fileupload']['name']),
	'name' => $img_dir_host.DIRECTORY_SEPARATOR.'new_name.png'
));
/*
// Try to upload file
try {
	// Success!
	$file->upload();

	// make thumbnails
	//


} catch (\Exception $e) {
	// Fail
	$errors = $file->getErrors();
	print_r($errors);
}
 */


?>
