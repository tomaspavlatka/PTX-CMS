<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 19.6.2010
**/

class Zend_View_Helper_TransParam {
    /************** VARIABLES **************/
    private $_translator;
    /************** PUBLIC FUNCTION **************/
    /**
     * konstruktor
     */
    public function __construct() {
        // nactem si translator z registru do privatni promenne
        $this->_translator = Zend_Registry::get('translate');
    }
    
    /**
     * prelozi zaslany retezec do spravneho jazyka
     * @param $string - retezec
     * @param $params - parametry
     * @param $escape - escape string
     * @param $escapeParams - escape params
     * @return prelozeny retezec
     */
    public function transParam($string,array $params,$escape = true,$escapeParam = true) {
        // ziskame prelozeny retezec
        if($escape) {
            $translatedString = htmlspecialchars(trim($this->_translator->_($string))); 
        } else {
            $translatedString = $this->_translator->_($string);
        }
        
        // projdem polem parametru
        foreach($params as $key => $value) {
            if($escapeParam) {
                $value = htmlspecialchars(trim($this->_translator->_($value)));
            }
            
            $translatedString = str_replace($key,$value,$translatedString);
        }
        
        // vratime hodnotu
        return $translatedString;
    }
}