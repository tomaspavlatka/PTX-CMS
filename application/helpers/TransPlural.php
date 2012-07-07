<?php
/**
 * Author: Tomas Pavlatka
 * Email: tomas.pavlatka@gmail.com
 * Created: 23.1.2010 19:22:57
 */
 
class Zend_View_Helper_TransPlural {
        
	private $_single;
	private $_plural;
	private $_plural2;
	
    public function transPlural($single, $plural, $next,$number, $escape = true, $locale = null) {
        
    	$this->_single = $single;
    	$this->_plural = $plural;
    	$this->_plural2 = $next;
        
        $this->locale = (empty($locale)) ? Zend_Registry::get('Zend_Locale') : $locale;
        
        switch($this->locale) {
        	case 'cs_CZ':
        		$string = $this->_czech($number);
        		break;
        	default:
        		$string = $this->_default($number);
                break;
        }
        
        // vratime spravny tvar
        if($escape) {
        	return htmlspecialchars(trim($string));
        } else {
        	return $string;
        }
    }
    
    private function _czech($number) {
    	switch($number) {
    		case 1:
                return $this->_single;
    		case 2:
            case 3:
            case 4:
            	return $this->_plural2;
            default:
            	return $this->_plural;
    	}
    }
    
    private function _default($number) {
        switch($number) {
            case 1:
                return $this->_single;
            default:
                return $this->_plural;
        }
    }
}