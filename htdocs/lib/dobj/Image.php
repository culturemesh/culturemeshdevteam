<?php
namespace dobj;

class Image extends DisplayDObj {

	protected $hash;
	protected $post;
	protected $event;
	protected $profile;
	protected $date_loaded;

	public function display($context) {

	}

	public static function createFromId($id) {
		return new Image();
	}

	public function insertImage($dal, $do2db) {
		return $do2db->execute($dal, $this, 'insertImage');
	}

	public function getHTML($context) {

	}

	public function convertToDir($hash) {

		$file_arr = str_split($hash);
		$filename_dir = '';
		$slash_pos = array(2, 4, 4, 4, 4, 4, 3);
		

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
