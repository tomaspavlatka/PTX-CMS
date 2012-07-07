<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Oct 17, 2011
 */
 
require_once APPLICATION_PATH.'/helpers/Trans.php';

class Zend_View_Helper_PtxUrl extends Zend_View_Helper_Url {
    
    /*
     * Trans (Private).
     */
    private $_trans;
    
    /*
     * Private locale (Private).
     */
    private $_locale;
    
    /**
     * Construct.
     * 
     * constructor of class.
     */
    public function __construct() {
        $config = Zend_Registry::get('config');
        $this->_locale = substr($config->ptx->project->locale,0,2);
    }
    
    /**
     * PTX Url (Public).
     * 
     * returns url
     * @param array $urlOptions
     * @param mixed $name
     * @param boolean $reset
     * @param boolean $encode
     */
    public function ptxUrl(array $urlOptions = array(),$name = null,$reset = false, $encode = true) {
        $lang = Zend_Registry::get('PTX_Locale');
        $this->_trans = new Zend_View_Helper_Trans();
        
        if(!isset($urlOptions['lang'])) {
            $urlOptions['lang'] = $lang;
        }

        // Get Url from Zend Framework.
        $url = $this->url($urlOptions,$name,$reset,$encode);
        $locale = 'cs';
        
        if(substr($url,0,4) == '/'.$this->_locale.'/') {
            return substr($url,3);
        } else if(substr($url,0,3) == '/'.$this->_locale) {
            return '/';
        } else {
            return $url;
        }
    }
}