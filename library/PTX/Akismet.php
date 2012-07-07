<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 9.11.2010
**/

class PTX_Akismet {
    
    /************ VARIABLES ************/
    public static $apiKey = 'c1561cfce46f';
    private $_obj;
    
    /************ PUBLIC FUNCTION ************/
    /**
     * constructor
     */
    public function __construct() {
        $this->_obj = new Zend_Service_Akismet(self::$apiKey,'http://'.$_SERVER['HTTP_HOST']); 
    }
    
    /**
     * return what akismet thinks about post
     * @param $formData - data about form
     * @return true - is spam | false - is not spam
     */
    public function isSpam($formData) {
        $data = array(
            'user_ip'              => $_SERVER['REMOTE_ADDR'],
            'user_agent'           => $_SERVER['HTTP_USER_AGENT'],
            'comment_type'         => 'comment',
            'comment_author'       => $formData['name'],
            'comment_content'      => $formData['message'],
        ); 
        
        return $this->_obj->isSpam($data);
    }
    
    /**
     * validation of api key
     * @return unknown_type
     */
    public function validateApiKey() {
        return ($this->_obj->verifyKey(self::$apiKey)) ? true : false;
    }
}