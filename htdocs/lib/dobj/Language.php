<?php
namespace dobj;

class Language extends Searchable {

	protected $name;

	public function toString() {
		return $this->name;
	}

	public static function createFromId($id, $dal, $do2db) {

		$language = new Language();
		$language->id = $id;

		$language = $do2db->execute($dal, $language, 'getLanguageById');

		if (get_class($language) == 'PDOStatement')
			return false;

		return $language;
	}
}

?>
