<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 24.7.2010
**/

class Default_Model_User extends Default_Model_AppModel {
    
    /************ PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     * @param $id - ID
     */
    public function __construct($id){
        $this->_id = (int)$id;
        $this->_dao = new Default_Model_DbTable_User();
    }
    
    /**
     * Update last login.
     * 
     * updates information about user last login
     * @return id of inserted record
     */
    public function updateLastLogin() {
        $paramsObj = new Default_Model_UserParams();
        return $paramsObj->updateLastLogin($this->_id);
    } 
    
    /**
     * Update password.
     * 
     * updates password for an user     
     * @param string $password - new password
     * @return number of affected rows in db
     */
    public function updatePassword($password) {
    	return $this->_dao->updatePassword($password,$this->_id);
    }
}