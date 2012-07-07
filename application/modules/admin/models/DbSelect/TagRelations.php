<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Oct 26, 2011
 */
 
class Admin_Model_DbSelect_TagRelations {
    
    /************* PUBLIC STATIC FUNCTION *************/
    /**
     * All list.
     * 
     * prepares Zend_DbSelect object for all records
     * @return Zend_DbSelect select 
     */
    public static function allList() {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        
        return $select->from('tag_relations');
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
        
        return $select->from('tag_relations',array());
    }
}