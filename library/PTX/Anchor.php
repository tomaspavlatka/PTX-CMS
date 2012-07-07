<?php
/**
 * Author: Tomas Pavlatka
 * Email: tomas.pavlatka@gmail.com
 * Created: 23.1.2010 22:41:45
 */
 
class PTX_Anchor {
    
    /**
     * ulozi kotvu a da ji do SESSION
     * @param $namespace - namespace pro SESSION
     * @param $value - hodnota, kt se ma nastavit
     * @param $key - klic, pod kt se schovava kotva
     */
    public static function createAnchor($namespace,$value = null,$key = 'anchor') {
        // pokud mame nejakou hodnotu
        if(!empty($value)) {
            // ulozime si kotvu do SESSION
            PTX_Session::setSessionValue($namespace,$key,$value);
        } else {
            // ulozime si kotvu do SESSION
            PTX_Session::setSessionValue($namespace,$key,$_SERVER['REQUEST_URI']);
        }
    }
    
    /**
     * vytahne kotvu ze SESSION
     * @param $namespace - namespace
     * @param $key - klic, pod kt se kotva ukryva
     * @return kotva
     */
    public static function getAnchor($namespace,$key = 'anchor') {
        // vratime kotvu
        return PTX_Session::getSessionValue($namespace,$key);
    }
    
    /**
     * get anchor from better style
     * @param $controller
     * @param $action
     * @param $module
     * @param $request - request     
     */
    public static function get($controllerName,$actionName,$moduleName = 'admin') {
        $sessionObj = new Zend_Session_Namespace('ptxanchor');
        
        if(isset($sessionObj->anchor[$moduleName][$controllerName][$actionName])) {
            return $sessionObj->anchor[$moduleName][$controllerName][$actionName];
        } else {
            return '/'.$moduleName.'/'.$controllerName.'/'.$actionName;
        }
    }
    
    /**
     * creates better style of anchor
     * @param $request - request     
     */
    public static function set(Zend_Controller_Request_Http $request) {
        
        $moduleName = $request->getModuleName();
        $controllerName = $request->getControllerName();
        $actionName = $request->getActionName();
        
        $sessionObj = new Zend_Session_Namespace('ptxanchor');
        
        if(!isset($sessionObj->anchor)) {
            $sessionObj->anchor = array();
        }
        
        $sessionObj->anchor[$moduleName][$controllerName][$actionName] = $request->getRequestUri();
    }
}