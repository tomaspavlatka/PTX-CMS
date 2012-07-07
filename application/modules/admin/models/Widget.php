<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 8.9.2010
**/

class Admin_Model_Widget extends Admin_Model_AppModel {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     * @param $id - ID
     */
    public function __construct($id) {
        $this->_dao = new Admin_Model_DbTable_Widget();
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

    /**
     * Change position.
     * 
     * changes position for record
     * @param $way - way
     */
    public function changePosition($way) {
        $this->_checkData();
        
        $data4Change = $this->_dao->find4PositionChange($this->_data->widget_place_id,$this->_data->user_id,$this->_data->position,$way);

        if(isset($data4Change->position)) {
            $this->_dao->updatePosition($this->_data->position,$data4Change->id);
            $this->_dao->updatePosition($data4Change->position,$this->_id);
        }
    }  
}