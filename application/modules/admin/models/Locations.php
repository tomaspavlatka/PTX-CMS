<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Oct 22, 2011
 */
 
class Admin_Model_Locations extends Admin_Model_AppModels {
    
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        $this->_dao = new Admin_Model_DbTable_Location();
    }
}
