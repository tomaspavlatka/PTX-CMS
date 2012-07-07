<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 19.8.2010
**/

class Zend_View_Helper_ViewCurrency {
    /************* PUBLIC FUNCTION *************/   
    /**
     * vrati hodnotu cisla 
     * @param $price - cena
     * @param $locale - vynucene Zend_Locale
     * @param $nullValue - co se ma vratit v pripade, ze mktime je prazdny
     * @return datum
     */
    public function viewCurrency($price,$locale = 'pl_PL', $nullValue = '&nbsp;',$showNullValue = false) {
        if(!empty($price) || ($showNullValue && is_numeric($price))) {
            $this->locale = (!empty($locale)) ? $locale : Zend_Registry::get('Zend_Locale');
            
            $currency = new Zend_Currency($this->locale);
            return $currency->toCurrency($price);
        } else {
            return $nullValue;
        }
    }
}