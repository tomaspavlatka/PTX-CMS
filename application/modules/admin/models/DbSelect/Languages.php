<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Oct 13, 2011
 */
  
class Admin_Model_DbSelect_Languages  {
    
    /**
     * All list.
     * 
     * prepares Zend_DbSelect object for all records
     * @return Zend_DbSelect select 
     */
    public static function allList() {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        
        return $select->from('languages');
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
        
        return $select->from('languages',array());
    }
    
    /**
     * Get Active.
     * 
     * returns list of active languages
     * @return array active languages
     */
    public static function getActive() {
    	$db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $locale = Zend_Registry::get('PTX_Locale');
        
        $select->from('languages',array())->columns(array('id','code','locale','name_'.$locale.' as name'))->where('status = 1')->order('position ASC');
        $stmt = $select->query();
        $data = $stmt->fetchAll();
        
        return (array)$data;
    }
}