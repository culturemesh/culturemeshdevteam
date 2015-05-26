<?php
namespace api;

class RelatedReplyEmail extends Email {
    
    protected function compose($cm, $mustache, $settings) {
        
        $this->subject = 'Related Reply';
        $template = file_get_contents($cm->template_dir . $cm->ds . 'email/related-reply.html');
        
        return $mustache->render($template, array(
		'vars' => $cm->getVars(),
		'reply' => $settings['reply'],
		'name' => $settings['reply']->getName()
	));
    }
    
    /*
     * Swaps address set in constructor for sender address,
     * puts address set in constructor in bcc header
     *
     */
    protected function reviewSettings($settings) {

	    // Add blind carbon copy addresses,
	    // don't want these people seeing each other's emails
	    $this->headers .= "\r\n";
	    $this->headers .= "Bcc: " . $this->address . "\r\n";

	    // set address equal to sender
	    $this->address = 'no-reply@culturemesh.com';
    }
}

?>
