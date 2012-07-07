<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 22.8.2010
**/

class Admin_Model_WidgetPlace extends Admin_Model_AppModel {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     * @param $id - ID
     */
    public function __construct($id) {
        $this->_dao = new Admin_Model_DbTable_WidgetPlace();
        $this->_id = (int)$id;
    }
    
    /**
     * Can be deleted.
     * 
     * checks whether record can be deleted.
     * @return true | false
     */
    public function canBeDeleted() {
        
        // Widgets.
        if($this->countWidgets(" > -1") > 0) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Count widgets.
     * 
     * counts how many widgets belongs to this place
     * @param $status - status
     * @return number
     */
    public function countWidgets($status = " > -1") {
        $select = Admin_Model_DbSelect_Widgets::pureSelect();
        $select->columns(array('COUNT(DISTINCT(id)) as count'))->where('widget_place_id = ?',(int)$this->_id)->where('status '.$status);
        $stmt = $select->query();
        $data = $stmt->fetchAll();
        
        return (isset($data[0]['count'])) ? (int)$data[0]['count'] : 0;
    }
}