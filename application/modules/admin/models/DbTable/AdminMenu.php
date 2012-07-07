<?php

class Admin_Model_DbTable_AdminMenu extends Admin_Model_DbTable_AppModel {
	
    /**
     * Construct.
     * 
     * constructor of class
     */
	public function __construct() {
        parent::__construct();
        $this->_name = 'admin_menus';
        $this->_transFields = array('name','description');
    }
    
    /** 
     * Find for position change.
     * 
     * find record in db suitable for change position
     * @param $idParent - ID of parent
     * @param $position - position
     * @param $way - way
     * @return data from db      
     */
    public function find4PositionChange($idParent,$position,$way) {
        $select = $this->select();
        $select->where('parent_id = ?',(int)$idParent)->where('status > -1');
        
        switch($way) {
            case 'down' :
                $select->where('position > ? ',(int)$position)->order('position asc');
                break;    
            case 'up' :
                $select->where('position < ? ',(int)$position)->order('position desc');
                break;
        }
        
        return $this->fetchRow($select);
    }
	
    /**
     * Update.
     * 
     * updates data in database
     * @param $formData - data to be updated
     * @param $id - ID
     * @param $position - position must be updated ?
     * @return number of affected rows in db
     */
    public function ptxUpdate(array $formData, $id,$updPosition) {
        $data = array(
            'parent_id'  => $formData['parent'],
            'module'     => $formData['module'],
            'controller' => $formData['controller'],
            'action'     => $formData['action'],
            'parameters' => $formData['params'],
            'status'     => $formData['status']);
            
        // Update position.        
        if($updPosition) {
            $data['position'] = $this->_findPosition($formData['parent']);
        }
        
        // Bind translations.
        $transFields = $this->_bindTransFields($formData,$data);
        
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
     * saves new record into database
     * @param $data - data to be saved     
     * @return id of new saved record
     */
    public function save(array $formData) {
        $data = array(
            'parent_id'  => (int)$formData['parent'],
            'module'     => $formData['module'],
            'controller' => $formData['controller'],
            'action'     => $formData['action'],
            'parameters' => $formData['params'],
            'status'     => (int)$formData['status'],
            'position'   => $this->_findPosition($formData['parent']));
        
        // Bind translations.
        $transFields = $this->_bindTransFields($formData,$data);
        
        $id = parent::insert($data);
        if(is_numeric($id)) {
            $this->_saveLog($data);
        }
        return (int)$id; 
    }
}