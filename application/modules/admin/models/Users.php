<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 25.7.2010
**/

class Admin_Model_Users extends Admin_Model_AppModels {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class.
     */
    public function __construct() {
        $this->_dao = new Admin_Model_DbTable_User();
    }
    
    
    /**
     * User exists.
     * 
     * checks whether user exists in database.
     * @param $email - email 
     * @param $idExclude - ID to be excluded
     * @return true | false
     */
    public function userExists($email,$idExclude = null) {
        $data = $this->_dao->findByEmail($email);
        
        foreach($data as $row) {
            if($row->id != $idExclude) {
                return true;
            } else {
                return false;
            }
        }
        
        return false; 
    }
}