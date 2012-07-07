<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 18, 2010
**/

class Admin_Model_TagRelations extends Admin_Model_AppModels {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class.
     */
    public function __construct() {
        $this->_dao = new Admin_Model_DbTable_TagRelation();
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
        return $this->_dao->clear4Parent($idParent,$parentType);
    }
    
    /**
     * Exists.
     * 
     * cheks whether this combination is already in db
     * @param $idTag - id tag
     * @param $idParent - id parent
     * @param $parentType - parent type
     * @return true | false
     */
    public function exists($idTag,$idParent,$parentType) {
        $data = $this->_dao->getData4Exists($idTag,$idParent,$parentType);
        
        return ($data instanceof Zend_Db_Table_Rowset_Abstract && isset($data[0]));
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
     * Save.
     * 
     * saves data into database     
     * @param array $formData - data to be saved
     * @param array $params - additional params
     * @return id of inserted record 
     */
    public function save(array $formData, array $params = array()) {
        // Clear all old records in db.
        if(isset($params['clear']) && $params['clear'] == true) {
            $this->clear4Parent($formData['parent_id'],$formData['parent_type']);
        }
        
        $data = $this->_dao->getData4Exists($formData['tagid'],$formData['parentid'],$formData['parenttype']);
        if($data instanceof Zend_Db_Table_Rowset) {
            $data = $data->toArray();
            
            if(empty($data)) {
                $save = true;    
            } else {
                $save = false;
            }
        } else { 
            $save = true;
        }
        
        if($save) {
            return $this->_dao->save($formData,$params);
        }
    }
}