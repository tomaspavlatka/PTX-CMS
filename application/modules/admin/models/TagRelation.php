<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Oct 27, 2011
 */
 
class Admin_Model_TagRelation extends Admin_Model_AppModel {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     * @param $id - ID
     */
    public function __construct($id) {
        $this->_dao = new Admin_Model_DbTable_TagRelation();
        $this->_id = (int)$id;
    }
    
    /**
     * Can be deleted.
     * 
     * checks whether record can be deleted.
     * @return true | false
     */
    public function canBeDeleted() {
        return true;
    }
}
