<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 23.8.2010
**/

class Article_Model_Article extends Default_Model_AppModel {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     * @param $id - ID
     */
    public function __construct($id) {
        $this->_dao = new Article_Model_DbTable_Article();
        $this->_id = (int)$id;
    }    
    
    
    /**
     * Update shown.
     * 
     * updates information how many time article has been shown
     * @return number of affected rows in db
     */
    public function updateShown() {
        $this->_checkData(false);
        
        $newValue = $this->_data->shown + 1;
        return $this->_dao->updateShown($newValue,$this->_id); 
    }
}