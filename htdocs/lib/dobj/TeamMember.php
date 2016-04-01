<?php
namespace dobj;

class TeamMember extends DisplayDObj {

	protected $name;
	protected $job_title;
	protected $bio;
	protected $team_member_since;
	protected $thumb_url;

	public function display($context) {

	}

	public function getHTML($context, $vars) {

		// get vars
		$cm = $vars['cm'];
		$mustache = $vars['mustache'];

		switch($context) {

		case 'about':

			// get template
			$template = file_get_contents($cm->template_dir . $cm->ds . 'about_team-member.html');

			return $mustache->render($template, array(
				'member' => $this));
		}
	}
}

?>
