<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 22.8.2010
**/

class Default_Model_StaticPage extends Default_Model_AppModel {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     * @param $id - ID
     */
    public function __construct($id) {
        $this->_dao = new Default_Model_DbTable_StaticPage();
        $this->_id = (int)$id;
    }
    
    /**
     * Update shown.
     * 
     * updates information about how many times records has been shown     
     * @return number of affected rows in db
     */
    public function updateShown() {
        $this->_checkData(false);
        $value = $this->_data->shown + 1;
        return $this->_dao->updateShown($value,$this->_id);
    }
}