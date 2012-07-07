<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Oct 26, 2011
 */
 
class Admin_Model_DbSelect_CategoryRelations {
    
    /**
     * All list.
     * 
     * prepare obj for retrieve all columns from db
     * @return Zend_DbSelect select 
     */
    public static function allList() {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        
        return $select->from('category_relations');
    }
    
    /**
     * Get 4 Parent.
     * 
     * returns data for parent.
     * @param string $parentType - type of parent
     * @param integer $parentId - id of parent
     * @param string $status - status of records
     * @return array with id of categories
     */
    public function get4Parent($parentType, $parentId, $status = " > -1") {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        
        $select = Admin_Model_DbSelect_CategoryRelations::pureSelect();
        $select->columns('DISTINCT(category_id)')->where('status '.$status)->where('parent_type = ?',$parentType)->where('parent_id = ?',(int)$parentId);
        $stmt = $select->query();
        $data = $stmt->fetchAll();
        
        $idsArray = PTX_Page::getIdsArray($data,'category_id');
        
        return (array)$idsArray;
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
        
        return $select->from('category_relations',array());
    }
}