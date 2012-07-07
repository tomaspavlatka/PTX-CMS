<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Oct 1, 2011
 */
  
class Admin_Model_Setting extends Admin_Model_AppModel {
    
    /**
     * Constructor
     * 
     * constructor of class
     * @param $id - ID of category
     */
    public function __construct($id) {
        $this->_dao = new Admin_Model_DbTable_Setting();
        $this->_id = (int)$id;
    }
    
    /**
     * Can be deleted.
     * 
     * check whether category can be deleted
     * @return true | false
     */
    public function canBeDeleted() {
        return true;
    }
}