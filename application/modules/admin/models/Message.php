<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 25.7.2010
**/

class Admin_Model_Message extends Admin_Model_AppModel {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     * @param $id - ID
     */
    public function __construct($id) {
        $this->_dao = new Admin_Model_DbTable_Message();
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
    
    /**
     * Get neighbor message
     * 
     * returns message which is next or previous
     * @param $type - next | previous
     * @return data about message
     */
    public function getNeighborMessage($type) {
        $this->_checkData(false);
        
        return $this->_dao->getNeighborMessage($type, $this->_data->user_id_to, $this->_data->created);
    }
    
    /**
     * Mark as read.
     * 
     * marks message as read
     * @return number of affected rows in db
     */
    public function markAsRead() {
        return $this->_dao->markAsRead($this->_id);    
    }
}