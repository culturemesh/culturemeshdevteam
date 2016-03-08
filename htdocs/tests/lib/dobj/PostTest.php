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

	public function testFormatTextAutoLink_2() {

		$post = new dobj\Post();
		$post->post_text = "Michael Resin's Album \"Emotion Sickness\" in Japan, China, Taiwan, Malaysia and more Bongo Boy Records https://t.co/xebVOJXDge via @sharethis";

		$formatted = $post->formatText();

		$this->assertEquals("Michael Resin's Album \"Emotion Sickness\" in Japan, China, Taiwan, Malaysia and more Bongo Boy Records <a target='_blank' href='http://t.co/xebVOJXDge'>https://t.co/xebVOJXDge</a> via @sharethis", $formatted);
	}

	public function testFormatTextAutoLink_3() {

		$post = new dobj\Post();
		$post->post_text = 'http://t.co/N4jIT89Hrk';

		$formatted = $post->formatText();

		$this->assertEquals("<a target='_blank' href='http://t.co/N4jIT89Hrk'>http://t.co/N4jIT89Hrk</a>", $formatted);
	}

	public function testFormatTextAutoLink_4() {

		$post = new dobj\Post();
		$post->post_text = 'MAPPE MONDE, WORLD MAP, NORTH POLE AND ANTARCTICA, EUGENE BELIN 1890 https://t.co/gsanF1qpp5 https://t.co/EYJuPXXLVw';

		$formatted = $post->formatText();

		$this->assertEquals("MAPPE MONDE, WORLD MAP, NORTH POLE AND ANTARCTICA, EUGENE BELIN 1890 <a target='_blank' href='http://t.co/gsanF1qpp5'>https://t.co/gsanF1qpp5</a> <a target='_blank' href='http://t.co/EYJuPXXLVw'>https://t.co/EYJuPXXLVw</a>", $formatted);
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
	
	public function testEliminateEllipsis2() {

		$post = new dobj\Post();
		$post->post_text = 'I...again twisst';

		$formatted = $post->formatText();

		$this->assertEquals("I...again twisst", $formatted);
	}

	public function testHTMLPurifyBasic_OpeningTags() {

		$post = new dobj\Post();
		$post->post_text = '[i]I...again [i]twisst';

		$formatted = $post->formatText();

		$this->assertEquals("<i></i>I...again <i></i>twisst", $formatted);
	}

	public function testHTMLPurifyBasic_ClosingTags() {

		$post = new dobj\Post();
		$post->post_text = '[/i]I...again [/i]twisst';

		$formatted = $post->formatText();

		$this->assertEquals("<i></i>I...again <i></i>twisst", $formatted);
	}
}

?>
