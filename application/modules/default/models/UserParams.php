<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 24.7.2010
**/

class Default_Model_UserParams extends Default_Model_AppModel {

    /************ PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct(){
        $this->_dao = new Default_Model_DbTable_UserParam();
    }
    
    /**
     * Update last login.
     * 
     * updates information about user last login
     * @param $id - ID
     * @return id of inserted record
     */
    public function updateLastLogin($id) {
        return $this->_dao->updateLastLogin($id);
    }
}