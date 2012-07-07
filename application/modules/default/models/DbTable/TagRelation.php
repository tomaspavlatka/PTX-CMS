<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 18, 2010
**/

class Default_Model_DbTable_TagRelation extends Default_Model_DbTable_AppModel {
    
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
     * Get data 4 tag.
     * 
     * return data for tag
     * @param $idTag - id tag
     * @param $parentType - parent type
     * @return data from db (fetchAll)
     */
    public function get4Tag($idTag,$parentType) {
        $select = $this->select()->where('tag_id = ?',(int)$idTag)->where('status > -1');
        
        // If we shoudl restrict to parent type.
        if(!empty($parentType)) {
            $select->where('parent_type = ?',$parentType);
        }
        
        return parent::fetchAll($select);
    }
}