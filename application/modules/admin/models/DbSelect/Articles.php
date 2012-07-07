<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 23.8.2010
**/

class Admin_Model_DbSelect_Articles {
    
    /************* PUBLIC STATIC FUNCTION *************/
     /**
     * All list.
     * 
     * prepares Zend_DbSelect object for all records
     * @return Zend_DbSelect select 
     */ 
     static function allList() {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        
        return $select->from('articles');
    }
    
    /**
     * Pure select.
     * 
     * prepares Zend_DbSelect object for ->columns(array(...)) parameters
     * @return Zend_DbSelect select 
     */
    public static function pureSelect() {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        
        return $select->from('articles',array());
    }
}