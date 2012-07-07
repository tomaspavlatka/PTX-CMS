<?php
/**
 * Author: Tomas Pavlatka
 * Email: tomas.pavlatka@gmail.com
 * Created: 23.1.2010 22:39:50
 */
 
class PTX_Session {
    /************ VARIABLE ************/
    
    /************ PUBLIC FUNCTION ************/
    
    /************ PUBLIC STATIC FUNCTION ************/    
    /**
     * ziska hodnotu ze SESSION
     * @param $namespace - namespace pro SESSION
     * @param $key - klic, pod kt se schovava hodnota, kt chceme znat
     */
    public static function getSessionValue($namespace,$key) {
        // zapnem si session
        $session = new Zend_Session_Namespace($namespace);
        
        // nastavime hodnotu
        return $session->$key;
    }
    
    /**
     * ulozi novou hodnotu do SESSION
     * @param $namespace - namespace pro SESSION
     * @param $key - klic, pod kt chceme hodnotu schovat
     * @param $value - hodnota, kt se ma nastavit
     */
    public static function setSessionValue($namespace,$key,$value) {
        // zapnem si session
        $session = new Zend_Session_Namespace($namespace);
        
        // nastavime hodnotu
        $session->$key = $value;
    }
    
    /************ PRIVATE FUNCTION ************/
}