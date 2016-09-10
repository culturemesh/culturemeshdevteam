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
	 * 	engine -
	 * 	thumbnail - true or false
	 * 	size - in pixels (sq or rect)
	 * 	suffix - profile, post, event, crowdfunding)
	 */
	protected $options;
	protected $cm;
	protected $divisions;
	protected $imgng;
	protected $files;

	public function __construct($cm, $options) {

		// give an environment
		$this->cm = $cm;

		// set count on separation for file md5
		$this->divisions = array(2, 4, 4, 4, 4, 4, 4, 3, 3);

		// exceptions
		if (!isset( $options['dir']))
			throw new \Exception('No directory was specified');
		if (!isset( $options['postname'] ))
			throw new \Exception('No post files were specified');

		// set default values
		if (!isset( $options['validation_size'] ))
			$options['validation_size'] = '2M';
		if (!isset( $options['validation_type'] ))
			$options['validation_type'] = array('image/png', 'image/gif', 'image/jpeg'); 

		// work on thumbnail options
		if (isset( $options['thumbnail'] )) {
			if (!isset( $options['thumbnail'])) 
				throw new \Exception('Class of thumbnail not specified');
		}

		// set options
		$this->options = $options;

		$this->files = array();
	}

	public function upload() {

		$storage = new \Upload\Storage\FileSystem($this->options['dir'], true);
		$files = new \Upload\File($this->options['postname'], $storage);

		// Validate file upload
		// MimeType List => http://www.webmaster-toolkit.com/mime-types.shtml
		$files->addValidations(array(
		    // Ensure file is of type "image/png"

		    //You can also add multi mimetype validation
		    new \Upload\Validation\Mimetype($this->options['validation_type']),

		    // Ensure file is no larger than 5M (use "B", "K", M", or "G")
		    new \Upload\Validation\Size($this->options['validation_size'])
		));

		// change names based on md5 of files
		foreach ($files as $file) {

			$nn = '';

			$md5 = $file->getMd5();
			$file->setExtension('png');

			// create new images
			$img = new \dobj\Image();
			$img->hash = $md5;
			$img->post = 1;
			array_push($this->files, $img);

			$c = 0;
			$o = 0;

			while($c < count($this->divisions)) {

				for ($i = 0; $i < $this->divisions[$c]; $i++) 
					$nn .= $md5[$o + $i];

				// we have the new directory name
				// ...make it
				if (count($this->divisions) - $c == 2) {
					$dir = $this->cm->img_repo_dir . $this->cm->ds . $nn;

					if (!file_exists($dir))
						mkdir($dir, 0777, true);
				}

				// add slash if not at end
				if (count($this->divisions) - $c > 1) 
					$nn .= $this->cm->ds;

				// increment counters
				$o += $this->divisions[$c];
				$c++;
			}

			// add extension
			$file->setName($nn);

		}



		try {
			$files->upload();

			if (isset($this->options['thumbnail'])) {
				try {
				  $success = $this->createThumbnails($files);
				}
				catch (\except\MissingClassException $e) {
				  return array( 'files' => $this->files,
					  'error' => $e->getMessage()
				  );
				}

				if ($success) 
				  return array( 'files' => $this->files);
				else
				  return array( 'error' => 'Thumbnails not created');
			}
			/*
			// create thumbnails
			if (isset($this->options['thumbnail'])) {

				try {
					$this->createThumbnails()

				if ($this->createThumbnails($files)) {
					return array(
						'files' => $this->files
					);
				}
				else {
					return array(
						'error' => 'Thumbnail not created'
					);
				}
			}
			 */
			

		}
		catch (\Exception $e) {

			return array(
				'error' => $e->getMessage()
			);
		}
	}

	private function createThumbnails($files) {

		$errors = 0;
		$dir = $this->cm->img_repo_dir . $this->cm->ds;

		foreach ($files as $file) {

			if (!class_exists('\Imagick')) {
				throw new \except\MissingClassException('Imagick is not defined');
			}

			// create Imagick
			$file_image = new \Imagick($dir . $file->getNameWithExtension());

			$suffix = NULL;
			$size = NULL;

			switch ($this->options['thumbnail']['class']) {

			case 'post':
				$suffix = 'pthumb';
				$size = 120;
				break;
			}
			// get path details
			$new_name = $dir . $file->getName() .'_'. $suffix . '.' . $file->getExtension();
			$relative_path = '../../user_images/' . $file->getName() .'thumb.' . $file->getExtension();

			// create thumbnail
			$file_image->thumbnailImage($size, 0);
			$file_image->setImageFormat ("png");
			$success = file_put_contents ($new_name, $file_image); // works, or:

			if ($success === False) {
				$errors++;
			}
		}

		if ($errors == 0)
			return true;
		else
			return false;
	}
}

?>
