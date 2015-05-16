<?php
namespace api;

class PostReplyEmail extends Email {
    
    protected function compose($cm, $mustache, $settings) {
        
        $this->subject = 'Someone Replied to Your Post';
        $template = file_get_contents($cm->template_dir . $cm->ds . 'email/post-reply.html');
        
        return $mustache->render($template, array(
                    'something' => true));
    }
    
    protected function reviewSettings($settings) {
        
    }
}

?>