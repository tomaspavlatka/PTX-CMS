<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 24.7.2010
**/

class Default_Model_Users  extends Default_Model_AppModels {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        $this->_dao = new Default_Model_DbTable_User();
    }
    
    /**
     * Find user by email.
     * 
     * finds user according to his email
     * @param $email - email
     * @return data about user | null
     */
    public function findUserByEmail($email) {
        return $this->_dao->findUserByEmail($email);
    }
}