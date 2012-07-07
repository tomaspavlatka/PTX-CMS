<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 18, 2010
**/

class Admin_Model_DbSelect_Tags {
    
    /**
     * All list.
     * 
     * prepares Zend_DbSelect object for all records
     * @return Zend_DbSelect select 
     */
    public static function allList() {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        
        return $select->from('tags');
    }
    
    /**
     * Get List.
     * 
     * returns tags in list
     * @param string $locale - locale
     * @param array $mandatory - what columns are mandatory
     * @return array with tags.
     */
    public static function getList($locale, array $mandatory = array()) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        
        $select = new Zend_Db_Select($db);
        $select->from('tags',array())->columns(array('id','name_'.$locale.' as name'))
            ->where('status > -1')->order('name_'.$locale.' ASC');
            
        foreach($mandatory as $key => $value) {
            $select->where($value.'_'.$locale.' != ""');
        }
        
        $stmt = $select->query();
        $data = $stmt->fetchAll();
        
        $list = array();
        foreach($data as $key => $values) {
            $list[$values['id']] = $values['name'];
        }
        
        return (array)$list;
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
        
        return $select->from('tags',array());
    }
    
    
}