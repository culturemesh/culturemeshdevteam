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
    public function __construct($cm, $mustache, $address_arg, $settings) {
        
    	$this->headers = "From: no-reply@culturemesh.com" ."\r\n";
    	$this->headers .= "MIME-Version: 1.0" . "\r\n";
        $this->headers .= "Content-type: text/html; charset=iso-8859-1";
        
        // Make sure all the things required exist
        $this->reviewSettings($settings);
        
        // Set email address
	//// check for array
	if (is_array($address_arg)) {

		$this->address = '';

		for ($i = 0; $i < count($address_arg); $i++) {

			$this->address .= $address_arg[$i];

			// add comma
			if (count($address_arg) - $i > 1)
				$this->address .= ', ';
		}
	}
	else {
        	$this->address = $address_arg;
	}
        
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

    /*
     * Gets the html message for testing purposes
     * @returns - the html message
     */
    public function getMessage() {

	    return $this->message;
    }

    /*
     * Gets the to address for testing purposes
     * @returns - string: the email address
     */
	public function getAddress() {
		return $this->address;
	}
}

?>
