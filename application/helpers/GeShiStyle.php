<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 12.6.2010
**/

class Zend_View_Helper_GeShiStyle {
    
    public function geShiStyle($source,$language = 'php') {
        require_once '../library/geshi/geshi.php';
        
        $geshi = new GeSHi($source, $language);
        $geshi->set_header_type(GESHI_HEADER_PRE_VALID);
        
        return @$geshi->parse_code();
    }
}