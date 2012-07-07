<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 8.11.2010
**/

class Default_Model_DbTable_Relative extends Default_Model_DbTable_AppModel {
    
    /************* PUBLIC FUNCTION ***************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        
        $this->_name = 'relatives';
    }
    
    /**
     * Get 4 parent.
     * 
     * returns relatives for parent
     * @param $idParent - id article
     * @param $paent_type - type of parent
     * @param $status - status of relations
     * @return data from db (fetchAll)
     */
    public function get4Parent($idParent,$parentType,$status) {
        $select = $this->select()->where('parent_id = ?',(int)$idParent)->where('status '.$status)->where('parent_type = ?',$parentType);
        return parent::fetchAll($select);
    }
}