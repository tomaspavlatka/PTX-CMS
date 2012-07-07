<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 7.9.2010
**/

class Admin_Model_MenuInput extends Admin_Model_AppModel {
        
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     * @param $id - ID 
     */
    public function __construct($id) {
        $this->_dao = new Admin_Model_DbTable_MenuInput();
        $this->_id = (int)$id;
    }
    
    /**
     * Can be deleted.
     * 
     * checks whether record can be deleted
     * @return true | false
     */
    public function canBeDeleted() {
        return true;
    }
    
    /**
     * Change position.
     * 
     * changes position for record 
     * @param $way - smer
     * @return true | false
     */
    public function changePosition($way) {
        $this->_checkData();
        
        $data4Change = $this->_dao->find4PositionChange($this->_data->parent_id,$this->_data->menu_place_id,$this->_data->position,$way);

        if(isset($data4Change->position)) {
            $this->_dao->updatePosition($this->_data->position,$data4Change->id);
            $this->_dao->updatePosition($data4Change->position,$this->_id);
            
            return true;
        } else{
            return false;
        }
    } 
    
    /**
     * Update.
     * 
     * updates data in database
     * @param $formData - data to be updated
     * @return number of affected records in db
     */
    public function update(array $formData) {
        $this->_checkData(false,true);
        
        // Update position ?
        $updatePosition = ($this->_data['menu_place_id'] != $formData['place']) ? true : false;
        
        // Updata.
        return $this->_dao->ptxUpdate($formData,$this->_data,$updatePosition);
    }
}