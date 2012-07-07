<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Sep 23, 2011
 */
  
class Admin_Model_DbTable_Section extends Admin_Model_DbTable_AppModel {
    
    /************* PUBLIC FUNCTION ***************/
    /**
     * Constructor.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        $this->_name = 'sections';
        $this->_transFields = array('name');
    }
    
    /**
     * Update.
     * 
     * update data in database
     * @param $formData - data to be updated
     * @param $id - ID
     * @param $params - additional params
     * @return number of affected rows in db
     */
    public function ptxUpdate(array $formData, $id, array $params = array()) {
        $data = array(
            'updated'             => time(),
            'status'              => $formData['status']);
        
        // Bind translations.
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
     * save new record into database.
     * @param $formData - data to be saved.     
     * @param $params - additional params   
     * @return id of inserted record
     */
    public function save(array $formData, array $params = array()) {
        $data = array(
            'parent_type'         => $params['parent_type'],
            'created'             => time(),
            'updated'             => time(),
            'status'              => $formData['status']);
        
        // Bind translations.
        $this->_bindTransFields($formData,$data);
        
        $id = parent::insert($data);
        if(is_numeric($id)) {
            $this->_saveLog($data);
        }
        return (int)$id; 
    }
}