<?php
/**
 * Author: Tomas Pavlatka
 * Email: tomas.pavlatka@gmail.com
 * Created: 17.1.2010 18:57:53
 */
 
class Default_Plugin_LayoutSwitcher extends Zend_Controller_Plugin_Abstract{
    
    public function __construct() {}
    
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $module = $request->getModuleName();
        
        Zend_Layout::startMvc()->setOptions(array('layoutPath' => APPLICATION_PATH.'/layouts/'.$module));
    }
}