<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 28.7.2010
**/

class Admin_Model_UserParam extends Admin_Model_AppModel {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     * @param $id - ID     
     */
    public function __construct($id) {
        $this->_dao = new Admin_Model_DbTable_UserParam();
        $this->_id = (int)$id;
    }
    
    /**
     * Can be deleted.
     * 
     * checks where record can be deleted
     * @return true | false
     */
    public function canBeDeleted() {
        return true;
    }
}