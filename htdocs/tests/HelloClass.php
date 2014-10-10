<?php


class HelloClass
{

	private $hello;

	public function __construct($text) {

		// assign text
		$this->hello = $text;
	}

	public function getHello() {

		return $this->hello;
	}
}
