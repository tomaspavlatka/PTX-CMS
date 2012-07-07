<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 7.9.2010
**/

class Admin_Model_MenuPlace extends Admin_Model_AppModel {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     * @param $id - ID
     */
    public function __construct($id) {
        $this->_dao = new Admin_Model_DbTable_MenuPlace();
        $this->_id = (int)$id;
    }
    
    /**
     * Can be deleted.
     * 
     * checks whether record can be deleted
     * @return true | false
     */
    public function canBeDeleted() {
        // Menus.
        if($this->countMenus(" > -1") > 0) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Count menus.
     * 
     * counts how many menus belong to menu place
     * @param $status - status
     * @return number
     * TODO: Change conditions
     */
    public function countMenus($status = " > -1") {
//        $select = Admin_Model_DbSelect_MenuInputs::pureSelect();
//        $select->columns(array('COUNT(DISTINCT(id_menu)) as count'))->where('menu_place_id = ?',(int)$this->_id)->where('status '.$status);
//        $stmt = $select->query();
//        $data = $stmt->fetchAll();
//
//        return (isset($data[0]['count'])) ? (int)$data[0]['count'] : 0;
          return 0;
    }
}