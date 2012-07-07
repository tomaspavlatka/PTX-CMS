<?php
/**
 * Author: Tomas Pavlatka
 * Email: tomas.pavlatka@gmail.com
 * Created: 20.1.2010 8:03:21
 */
 
class Zend_View_Helper_Trans {
		
	public function trans($string,$escape = true) {
        $trans = Zend_Registry::get('translate');
        $locale = Zend_Registry::get('Zend_Locale');
        
        $translatedString = $trans->_($string,$locale);
        if($escape) {
        	return htmlspecialchars(trim($translatedString)); 
        } else {
        	return $translatedString;
        }
	}
}