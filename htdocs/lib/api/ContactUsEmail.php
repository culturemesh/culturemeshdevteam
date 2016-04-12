<?php
namespace api;

class ContactUsEmail extends Email {
    
    protected function compose($cm, $mustache, $settings) {
        
        $this->subject = 'Contact Us Message';
        $template = file_get_contents($cm->template_dir . $cm->ds . 'email/contact-us.html');
        
        return $mustache->render($template, array(
		'vars' => $cm->getVars(),
		'message' => $settings['message'],
		'name' => $settings['name'],
		'email' => $settings['email']
	));
    }
    
    protected function reviewSettings($settings) {

    }
}

?>
