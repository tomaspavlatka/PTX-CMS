<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Oct 22, 2011
 */
 
class Admin_Model_Tree_Location extends Admin_Model_Tree_AppModel {
    
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct(); 
        
        $this->_name = 'locations';
        $this->_transFields = array('name');
    }
}