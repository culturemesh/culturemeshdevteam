<?php

class PostTest extends PHPUnit_Framework_TestCase {

	/**
	 * @covers User::__construct
	 *
	 */
	public function testConstruct() {

		$post = new dobj\Post();
		$this->assertInstanceOf('dobj\Post', $post);
	}

	public function testFormatText() {

		$post = new dobj\Post();
		$post->post_text = '[b]Bold[/b]';
		$new_text = $post->formatText();

		$this->assertEquals('<b>Bold</b>', $new_text);

		$post->post_text = '[i]Italic[/i]';
		$new_text = $post->formatText();

		$this->assertEquals('<i>Italic</i>', $new_text);

		$post->post_text = '[link]Link[/link]';
		$new_text = $post->formatText();

		$this->assertEquals('<a target=\'_blank\' href=\'http://Link\'>Link</a>', $new_text);
	}
}

?>