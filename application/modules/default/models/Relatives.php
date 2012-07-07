<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 7.11.2010
**/

class Default_Model_Relatives extends Default_Model_AppModels {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        $this->_dao = new Default_Model_DbTable_Relative();
    }
    
    /**
     * Get 4 parent.
     * 
     * returns relatives for parent
     * @param $idParent - id article
     * @param $parentType - type of parent
     * @param $status - status of relations
     * @param $inArray - return result in array
     * @return relative articles
     */
    public function get4Parent($idParent, $parentType, $status = " > -1", $inArray = false) {
        $data = $this->_dao->get4Parent($idParent,$parentType,$status);
        
        if(!$inArray) {
            return $data;
        } else if($data instanceof Zend_Db_Table_Rowset) {
            return $data->toArray();
        } else {
            return array();
        }
    }
}
