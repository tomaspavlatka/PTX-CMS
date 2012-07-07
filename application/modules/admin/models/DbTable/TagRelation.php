<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 18, 2010
**/

class Admin_Model_DbTable_TagRelation extends Admin_Model_DbTable_AppModel {
    
    /************* PUBLIC FUNCTION ***************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        $this->_name = 'tag_relations';
    }
    
    /**
     * Clear 4 parent.
     * 
     * clears previous relations for parent
     * @param $idParent - ID of parent
     * @param $parentType - type of parent
     * @return number of affected rows in db.
     */
    public function clear4Parent($idParent,$parentType) {
        $where = "parent_id = ".(int)$idParent." AND parent_type = '".$parentType."'";
        return $this->delete($where);
    }
    
    /**
     * Get data 4 parent.
     * 
     * return data for parent
     * @param $idParent - id parent
     * @param $parentType - parent type
     * @return data from db (fetchAll)
     */
    public function get4Parent($idParent,$parentType) {
        $select = $this->select()->where('parent_id = ?',(int)$idParent)->where('parent_type = ?',$parentType)->where('status > -1');
        return parent::fetchAll($select);
    }
    
    /**
     * Get data 4 exists.
     * 
     * return data for following combination
     * @param $idTag - id tag
     * @param $idParent - id parent
     * @param $parentType - parent type
     * @return data from db (fetchAll)
     */
    public function getData4Exists($idTag,$idParent,$parentType) {
        $select = $this->select()->where('tag_id = ?',(int)$idTag)->where('parent_id = ?',(int)$idParent)->where('parent_type = ?',$parentType)->where('status > -1');
        return parent::fetchAll($select);
    }
    
    /**
     * Save.
     * 
     * save new record into db
     * @param array $formData - data from form
     * @param array $params - additional params
     * @return id of inserted record
     */
    public function save($formData, array $params = array()) {
        $data = array(
            'tag_id'      => (int)$formData['tagid'],
            'parent_id'   => (int)$formData['parentid'],
            'parent_type' => $formData['parenttype'],
            'status'      => 1);
        
        $id = parent::insert($data);
        if(is_numeric($id)) {
            $this->_saveLog($data);
        }
        return (int)$id;
    }
}