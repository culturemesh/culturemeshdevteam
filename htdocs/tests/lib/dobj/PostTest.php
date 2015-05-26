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

	public function testFormatTextAutoLink() {

		$post = new dobj\Post();
		$post->post_text = 'www.culturemesh.com';

		$formatted = $post->formatText();

		$this->assertEquals("<a target='_blank' href='http://www.culturemesh.com'>www.culturemesh.com</a>", $formatted);
	}

	public function testFormatTextMultipleLinks() {

		$post = new dobj\Post();
		$post->post_text = 'www.culturemesh.com [link]www.culturemesh.com[/link]';

		$formatted = $post->formatText();

		$this->assertEquals("<a target='_blank' href='http://www.culturemesh.com'>www.culturemesh.com</a> <a target='_blank' href='http://www.culturemesh.com'>www.culturemesh.com</a>", $formatted);
	}

	public function testEliminateEllipsis() {

		$post = new dobj\Post();
		$post->post_text = 'test...';

		$formatted = $post->formatText();

		$this->assertEquals("test...", $formatted);
	}
}

?>
