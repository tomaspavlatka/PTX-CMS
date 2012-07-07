<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Oct 6, 2011
 */
  
class FormController extends Zend_Controller_Action{
    
    public function init() {
        $response = $this->getResponse();
    }

    /**
     * Contact form.
     */
    public function contactFormAction() {
    	$msg = null;

        // Security.
        if(strstr($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])) {
	        $mandatory = array('name','email','message');
	        foreach($mandatory as $key => $value) {
	            if(!isset($_POST[$value])) {
	            	$msg = $this->view->trans('All mandatory fields must be filled');
	            } else {
	            	$postValue = (string)trim($_POST[$value]);
	            	if(empty($postValue)) {
	            		$msg = $this->view->trans('All mandatory fields must be filled');
	            	} else if($value == 'email') {
	            		$validator = new Zend_Validate_EmailAddress();
	            		if(!($validator->isValid($postValue))) {
	                        $msg = $this->view->trans('Email must be valid email address');
	            		}
	            	}
	            }
	        }
        } else {
        	$msg = $this->view->trans('You cannot send a message from different domain');
        }
        
        // Possible to send an email.
        if(empty($msg)) {
        	
        	// Mail.
        	$mailer = new Default_Model_Mailer('utf-8');
        	$mailer->contactForm($_POST);
        	
        	PTX_Session::setSessionValue('contact-form', 'form-data', array());
        	PTX_Message::setMessage4User('contact-form', 'done', $this->view->trans('Your message has been sent. Thank you.'));
        } else {
        	PTX_Message::setMessage4User('contact-form', 'error', $msg);
        	PTX_Session::setSessionValue('contact-form', 'form-data', $_POST);
        }
        $this->_redirect($_SERVER['HTTP_REFERER']);
    }
}

