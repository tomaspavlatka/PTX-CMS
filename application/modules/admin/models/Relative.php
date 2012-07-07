<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 16, 2010
**/

class Admin_Model_Relative extends Admin_Model_AppModel {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     * @param $id - ID
     */
    public function __construct($id) {
        $this->_dao = new Admin_Model_DbTable_Relative();
        $this->_id = (int)$id;
    }
    
    /**
     * Can be deleted.
     * 
     * checks whether record can be deleted
     * @return true | false
     */
    public function canBeDeleted() {
        return true;
    }
}