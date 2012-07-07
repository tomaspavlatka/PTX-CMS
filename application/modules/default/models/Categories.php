<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 23.8.2010
**/

class Default_Model_Categories extends Default_Model_AppModels {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        $this->_dao = new Default_Model_DbTable_Category();
    }
    
    /**
     * Find by hash.
     * 
     * finds record in db according to its url hash.
     * @param $hash - url hash
     * @param $status - status
     * @return data from db
     */
    public function findByHash($hash,$status = " > -1") {
        return $this->_dao->findByHash($hash,$status);
    }
}