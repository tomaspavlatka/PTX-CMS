<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 22.8.2010
**/

class Admin_Model_DbTable_WidgetPlace extends Admin_Model_DbTable_AppModel {
    
    /************* PUBLIC FUNCTION ***************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        $this->_name = 'widget_places';
        $this->_transFields = array('name');
    }
    
    /**
     * Update.
     * 
     * updates data in db
     * @param $formData - data to be updated
     * @param $id - ID
     * @return number of affected records in db
     */
    public function ptxUpdate(array $formData, $id) {
        $data = array(
            'status' => (int)$formData['status'],
            'parent_type' => $formData['type']);
        
        $this->_bindTransFields($formData,$data);
        
        $where = $this->getAdapter()->quoteInto('id = ?',(int)$id);
        
        $rows = parent::update($data,$where); 
        if($rows > 0) {
            $this->_saveLog($data,$where);
        }
        return (int)$rows;
    }
    
    /**
     * Save.
     * 
     * saves new record in db
     * @param $formData - data to be saved     
     * @return id of inserted record
     */
    public function save(array $formData) {
        $data = array(
            'status'     => (int)$formData['status'],
            'parent_type' => $formData['type']);
        
        $this->_bindTransFields($formData,$data);
        
        $id = parent::insert($data);
        if(is_numeric($id)) {
            $this->_saveLog($data);
        }
        return (int)$id; 
    }
}