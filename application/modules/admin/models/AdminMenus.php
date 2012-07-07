<?php
/**
 * Author: Tomas Pavlatka
 * Email: tomas.pavlatka@gmail.com
 * Created: 23.1.2010 22:58:10
 */
 
class Admin_Model_AdminMenus extends Admin_Model_AppModels {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     * @param $id - ID   
     */
    public function __construct() {
        $this->_dao = new Admin_Model_DbTable_AdminMenu();
    }
    
    /**
     * Get children.
     * 
     * return children for parent
     * @param $idParent - ID parent
     * @param $status - status
     * @return children
     */
    public function getChildren($idParent,$status) {
    	return $this->_dao->getChildren($idParent,$status);
    }
}