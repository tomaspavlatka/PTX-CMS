<?php
/**
 * Author: Tomas Pavlatka
 * Email: tomas.pavlatka@gmail.com
 * Created: 17.1.2010 13:21:15
 */
 
class Default_Bootstrap extends Zend_Application_Module_Bootstrap {
    
	protected function _initAutoload() {
        $fc = Zend_Controller_Front::getInstance();
    }
    
    public function _initViewHelpers() {
        $bootstrap = $this->getApplication();
        $layout = $bootstrap->getResource('layout');
        $view = $layout->getView();
        $view->addHelperPath(APPLICATION_PATH.'/modules/default/views/helpers/', '');
        
        ZendX_JQuery::enableView($view);
    }
}