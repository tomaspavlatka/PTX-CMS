<?php
/**
 * Author: Tomas Pavlatka
 * Email: tomas.pavlatka@gmail.com
 * Created: 17.1.2010 13:21:15
 */
 
class Admin_Bootstrap extends Zend_Application_Module_Bootstrap {
    
    /************* VARIABLES ************/
	private $_acl = null;
	
	/************* PROTECTED ************/
    protected function _initAutoload() {
        
        $fc = Zend_Controller_Front::getInstance();
        
        $this->_acl = new Default_Model_LibraryAcl();
        $fc->registerPlugin(new Admin_Plugin_Navigation($this->_acl));
    }
    
	public function _initViewHelpers() {
        $bootstrap = $this->getApplication();
        $layout = $bootstrap->getResource('layout');
        $view = $layout->getView();
        ZendX_JQuery::enableView($view);
    }
}