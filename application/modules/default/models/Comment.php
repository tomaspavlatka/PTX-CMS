<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 9.11.2010
**/

class Default_Model_Comment extends Default_Model_AppModel {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     * @param $id - ID
     */
    public function __construct($id) {
        $this->_dao = new Default_Model_DbTable_Comment();
        $this->_id = (int)$id;
    }    
    
    /**
     * Approve.
     * 
     * approves comment
     * @return number of affected rows in db
     */
    public function approve() {
        return $this->_dao->updateStatus(1,$this->_id);    
    }
    
    /**
     * Delete.
     * 
     * deletes comment
     * @return number of affected rows in db
     */
    public function delete() {
        return $this->_dao->updateStatus(-1,$this->_id);    
    }
}