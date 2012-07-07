<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 9.11.2010
**/

class Admin_Model_Comments extends Admin_Model_AppModels {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        $this->_dao = new Admin_Model_DbTable_Comment();
    }
    
    /**
     * Save.
     * 
     * saves new record into database
     * @param $formData - data to be saved.
     * @param $parentType - type of parent
     * @param $idParent - id of parent
     * @return id of inserted record into db
     */
    public function save($formData,$parentType,$idParent) {
        // Save.
        return $this->_dao->save($formData,$parentType,$idParent);
    }
}