<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 25.7.2010
**/

class Admin_Model_User extends Admin_Model_AppModel {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     * @param $id - ID
     */
    public function __construct($id) {
        $this->_dao = new Admin_Model_DbTable_User();
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
    
    /**
     * Upadate passwd.
     * 
     * updates password for user
     * @param $passwd - password
     * @return number of affected rows in db
     */
    public function updatePasswd($passwd) {
        return $this->_dao->updatePasswd($passwd,$this->_id);
    }
}