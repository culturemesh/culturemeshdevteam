<?php
namespace api;

class RelatedPTReplyEmail extends RelatedReplyEmail {
    
    protected function compose($cm, $mustache, $settings) {
        
        $this->subject = 'Related Reply';
        $template = file_get_contents($cm->template_dir . $cm->ds . 'email/related-pt-reply.html');
        
        return $mustache->render($template, array(
		'vars' => $cm->getVars(),
		'reply' => $settings['reply'],
		'name' => $settings['reply']->getName()
	));
    }
}

?>
