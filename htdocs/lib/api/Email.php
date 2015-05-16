<?php
namespace api;

abstract class Email {
    
    protected $address;
    protected $subject;
    protected $message;
    protected $headers;
    
    /*
     * Standard constructor
     */
    public function __construct($cm, $mustache, $address, $settings) {
        
    	$this->headers = "From: no-reply@culturemesh.com\n";
    	$this->headers .= "MIME-Version: 1.0\n";
        $this->headers .= "Content-type: text/html; charset=iso-8859-1";
        
        // Make sure all the things required exist
        $this->reviewSettings($settings);
        
        // Email address
        $this->address = $address;
        
        // create message based on specific email class
        $this->message = $this->compose($cm, $mustache, $settings);
    }
    
    /*
     *
     */
    abstract protected function compose($cm, $mustache, $settings);
    abstract protected function reviewSettings($settings);
    
    /*
     * Actually send your precious email
     * ... uses the mail() php function at the moment
     * @param - 
     * @returns - 
     */
    public function send() {
        
        return mail($this->address, $this->subject, $this->message, $this->headers);
    }
}

?>