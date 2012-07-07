<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 20, 2010
**/

class Admin_Model_Photo extends Admin_Model_AppModel {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Constructor
     * 
     * constructor of class
     * @param $id - ID 
     */
    public function __construct($id) {
        $this->_dao = new Admin_Model_DbTable_Photo();
        $this->_id = (int)$id;
    }
    
    /**
     * Can be deleted.
     * 
     * check whether record can be deleted
     * @return true | false
     */
    public function canBeDeleted() {
        return true;
    }
    
    /**
     * Update.
     * 
     * update data in db
     * @param $formData - data from form
     * @return number of affected rows in db
     */
    public function update(array $formData) {
        $this->_checkData(false);
        
        // Parent has been changed.
        $updPosition = ($this->_data->parent_id != $formData['parent']) ? true : false;
        
        // Make update.
        return $this->_dao->ptxUpdate($formData,$this->_id,$updPosition, $this->_data->parent_type);
    }
    
    /**
     * Update position.
     * 
     * updates position for record.
     * @param integer $position - new position
     * @return number of affected rows
     */
    public function updatePosition($position) {
        return $this->_dao->updatePosition($position,$this->_id);
    }
}