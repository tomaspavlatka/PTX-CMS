<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Sep 23, 2011
 */
  
class Admin_Model_Banner extends Admin_Model_AppModel {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Constructor
     * 
     * constructor of class
     * @param $id - ID of category
     */
    public function __construct($id) {
        $this->_dao = new Admin_Model_DbTable_Banner();
        $this->_id = (int)$id;
    }
    
    /**
     * Can be deleted.
     * 
     * check whether category can be deleted
     * @return true | false
     */
    public function canBeDeleted() {
        return true;
    }
    
    /**
     * Update.
     * 
     * updates data in database
     * @param array $formData - data to be updated
     * @param array $params - additional params
     * @return number of affected rows in db
     */
    public function update(array $formData, array $params = array()) {
        $data = $this->getData(true,true);
        
        if($formData['parent'] != $data['parent_id']) {
        	$params['position_update'] = true;
        } else {
        	$params['position_update'] = false;
        }
        
    	return $this->_dao->ptxUpdate($formData,$this->_id,$params);
    }
    
    /**
     * Update logo.
     * 
     * update logo for category
     * @param string $filename - filename for logo
     * @param array $imageSize - data about image
     * @return number of affected rows in db
     */
    public function updateLogo($filename, array $imageSize) {
        $this->_checkData(false);
        
        if($filename != $this->_data->image_file) {
            return $this->_dao->updateLogo($filename,$this->_id,$imageSize);
        }
    }
}