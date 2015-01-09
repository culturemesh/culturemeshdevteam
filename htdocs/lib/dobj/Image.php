<?php
namespace dobj;

class Image extends DisplayDObj {

	protected $hash;
	protected $post;
	protected $id_post;
	protected $event;
	protected $id_event;
	protected $profile;
	protected $id_profile;
	protected $date_loaded;
	protected $real_url;
	protected $host_url;

	public function __construct() {

		$this->post = 0;
		$this->event = 0;
		$this->profile = 0;
	}

	public function display($context) {

	}

	public static function createFromId($id, $dal, $do2db) {
		return new Image();
	}

	public function insert($dal, $do2db) {

		if (!isset($this->hash)) 
			throw new \Exception('No hash is set');

		if (strlen($this->hash) != 32)
			throw new \Exception('This hash is the wrong length');

		if ($this->post == 0 && $this->event == 0 && $this->profile == 0)
			throw new \Exception('Must specify an image type before inserting');

		$result = $do2db->execute($dal, $this, 'insertImage');
		$this->id = $dal->lastInsertId();

		var_dump($this);
	}

	public function getHTML($context, $vars) {

	}

	public function convertToDir($hash) {

		$file_arr = str_split($hash);
		$filename_dir = '';
		$slash_pos = array(2, 4, 4, 4, 4, 4, 4, 3, 3);
		
		$i = 0;
		while (isset($slash_pos[$i])) {

			$count = $slash_pos[$i];
			// countdown to next slash
			while($count > -1) {

				if ($count == 0 && $i < count($slash_pos) - 1) 
				  { $filename_dir .= DIRECTORY_SEPARATOR; }
				else 
				  { $filename_dir .= array_shift($file_arr); }

				$count--;
			}

			// increment i 
			$i++;
		}

		return $filename_dir;
	}
}

?>
