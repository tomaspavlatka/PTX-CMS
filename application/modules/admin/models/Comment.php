<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 9.11.2010
**/

class Admin_Model_Comment extends Admin_Model_AppModel {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     * @param $id - ID
     */
    public function __construct($id) {
        $this->_dao = new Admin_Model_DbTable_Comment();
        $this->_id = (int)$id;
    }    
    
    /**
     * Can be delete.
     * 
     * checks whether record can be deleted
     * @return true | false
     */
    public function canBeDeleted() {
        // TODO.
        return true;
    }
}