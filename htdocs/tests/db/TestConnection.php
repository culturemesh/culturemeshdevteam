<?php

class TestConnection
{

	public static function tcPDO() {

		return new PDO('sqlite::memory:');
	}
}
?>
