<?php
namespace misc;

class ImageUpload {

	/*    ------- OPTIONS --------
	 *
	 * > dir - name of expected file place
	 * > postname - name of post for file access
	 * > validation_type - what type of files
	 * > validation_size - how big the files should be
	 * > thumbnail - (array
	 * 	thumbnail - true or false
	 * 	size - in pixels (sq or rect)
	 * 	suffix - profile, post, event, crowdfunding)
	 */
	protected $options;

	public function __construct($options) {
		$this->options = $options;
	}

	public function upload() {

		$img_dir = Environment::$site_root.'/devlab/images';
		$img_dir_host = $cm->host_root.'/devlab/images';

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
	}
}

?>
