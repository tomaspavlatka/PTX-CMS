<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Oct 21, 2011
 */
 
class Default_Plugin_Locale extends Zend_Controller_Plugin_Abstract {
    
    /**
     * Pre Dispatch.
     * 
     * pre dispatch
     * @see Zend/Controller/Plugin/Zend_Controller_Plugin_Abstract::preDispatch()
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        // Variables.
        $requestParams = $request->getParams();
        
        if(isset($requestParams['lang'])) {
            Zend_Registry::set('PTX_Locale',$requestParams['lang']);
            
            if($requestParams['lang'] == 'cs') {
                Zend_Registry::set('Zend_Locale','cs_CZ');
            } else if($requestParams['lang'] == 'en') {
                Zend_Registry::set('Zend_Locale','en_UK');
            }  
        } else {
            Zend_Registry::set('PTX_Locale','cs');
            Zend_Registry::set('Zend_Locale','cs_CZ');
        }
    }
}