<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 18, 2010
**/

class Default_Model_TagRelations extends Default_Model_AppModels {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class.
     */
    public function __construct() {
        $this->_dao = new Default_Model_DbTable_TagRelation();
    }
    
    /**
     * Get 4 parent.
     * 
     * returns tags for parent
     * @param $idParent - id parent
     * @param $parentType - parent type
     * $parem $type - type of result
     * @return tags for parent
     */
    public function get4Parent($idParent,$parentType, $type = 'db') {
        $data = $this->_dao->get4Parent($idParent,$parentType);
        
        if($type == 'db') {
            return $data;
        } else if($type == 'array') {
            if($data instanceof Zend_Db_Table_Rowset_Abstract) {
                return $data->toArray();
            } else {
                return array();
            }
        } else if($type == 'list') {
            $list = array();
            foreach($data as $row) {
                $list[$row['id']] = $row['tag_id'];
            }
            
            return $list;
        }
    }
    
    /**
     * Get 4 tag.
     * 
     * returns data for tag
     * @param $idTag - id tag
     * @param $parentType - parent type
     * $parem $type - type of result
     * @return tags for parent
     */
    public function get4Tag($idTag,$parentType = null, $type = 'db') {
        $data = $this->_dao->get4Tag($idTag,$parentType);
        
        if($type == 'db') {
            return $data;
        } else if($type == 'array') {
            if($data instanceof Zend_Db_Table_Rowset_Abstract) {
                return $data->toArray();
            } else {
                return array();
            }
        } else if($type == 'ids_array') {
            $idsArray = array();
            foreach($data as $row) {
                if(!array_key_exists($row['parent_type'],$idsArray)) {
                     $idsArray[$row['parent_type']] = array($row['parent_id']);
                } else {
                    $idsArray[$row['parent_type']][] = $row['parent_id'];
                }
            }
            
            return $idsArray;
        }
    }
}