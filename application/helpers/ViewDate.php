<?php
/**
 * Author: Tomas Pavlatka
 * Email: tomas.pavlatka@gmail.com
 * Created: 30.1.2010 18:05:33
 */

class Zend_View_Helper_ViewDate{
	/************* PUBLIC FUNCTION *************/	
    /**
     * vrati datum 
     * @param $mktime - mktime
     * @param $dateConst - konstanta pro datum, viz Zend_Date
     * @param $locale - vynucene Zend_Locale
     * @param $nullValue - co se ma vratit v pripade, ze mktime je prazdny
     * @return datum
     */
    public function viewDate($mktime,$dateConst = Zend_Date::DATETIME_SHORT,$locale = null, $nullValue = '&nbsp;') {
    	if(!empty($mktime)) {
	        $this->locale = (!empty($locale)) ? $locale : Zend_Registry::get('Zend_Locale');
	        
            $date = new Zend_Date($mktime,null,$this->locale);
            return $date->get($dateConst);
    	} else {
    		return $nullValue;
    	}
    }
}