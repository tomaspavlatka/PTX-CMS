<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 20, 2010
**/

class Admin_Model_DbSelect_Photos {
    
    /************* PUBLIC STATIC FUNCTION *************/
    /**
     * All list.
     * 
     * prepare obj for retrieve all columns from db
     * @return Zend_DbSelect select 
     */
    public static function allList() {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        
        return $select->from('photos');
    }
    
    /**
     * Pure select.
     * 
     * prepare obj for retrieve columns specified in ->columns(array()) field
     * @return Zend_DbSelect select 
     */
    public static function pureSelect() {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        
        return $select->from('photos',array());
    }
}