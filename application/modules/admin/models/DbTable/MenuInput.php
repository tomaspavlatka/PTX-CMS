<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 7.9.2010
**/

class Admin_Model_DbTable_MenuInput extends Admin_Model_DbTable_AppModel {
    
    /************* PUBLIC FUNCTION ***************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        
        $this->_name = 'menu_inputs';
        $this->_transFields = array('name','title','link');
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
    public function find4PositionChange($idParent,$idMenuPlace,$position,$way) {
        $select = $this->select();
        $select->where('menu_place_id = ?',(int)$idMenuPlace)->where('parent_id = ?',(int)$idParent)->where('status > -1');
        
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
     * updates data in databse
     * @param $formData - data to be updated
     * @param $inputData - original data
     * @param $updatePosition - update position ?
     * @return number of affected rows in db
     */
    public function ptxUpdate($formData,$inputData,$updatePosition) {
        $data = array(
            'target'   => (int)$formData['target'],
            'updated'  => time(),
            'parent_id'   => (int)$formData['parent'],
            'status'   => (int)$formData['status']);
        
        if($updatePosition) {
            $data['position'] = $this->_findPosition($formData['place']);
        }
        
        // Bind translation.
        $this->_bindTransFields($formData,$data);
        
        $params = array('type' => $inputData['parent_type']);
        $data = $this->_completeArray($formData,$data,$params);
        
        
        $where = $this->getAdapter()->quoteInto('id = ?',(int)$inputData['id']); 
        $row = parent::update($data,$where);
        if($row > 0) {
            $this->_saveLog($data,$where);
        }
        return (int)$row; 
    }
    
    /**
     * Save.
     * 
     * saves new record into db
     * @param array $formData - data to be saved     
     * @param arary $params - additional params
     * @return id of inserted record in db
     */
    public function save(array $formData,array $params) {
        $data = array(
            'menu_place_id'    => (int)$params['place'],
            'parent_type'      => $params['type'],
            'parent_id'        => (int)$formData['parent'],
            'target'           => (int)$formData['target'],
            'position'         => $this->_findPosition($params['place']),
            'created'          => time(),
            'updated'          => time(),
            'status'           => (int)$formData['status']);
        
        // Bind translation.
        $this->_bindTransFields($formData,$data);
        $data = $this->_completeArray($formData,$data,$params);
        
        $id = parent::insert($data);
        if(is_numeric($id)) {
            $this->_saveLog($data);
        }
        return (int)$id; 
    }
    
    /**
     * zkompletuje data pro dany typ widgetu
     * @param $formData - form data
     * @param $data - data 
     * @param $type - typ
     * @return data pro dalsi praci
     */
    private function _completeArray($formData,$data,$params) {        
        if($params['type'] == 'staticpage' || $params['type'] == 'article') {
            $data['input_id'] = (int)$formData['input'];
        } else if($params['type'] == 'category') {
            $data['input_id'] = (int)$formData['input'];
            $data['help_type'] = $params['parent_type'];
        }
        
        return $data;
    }
    
    
    /**
     * Find position.
     * 
     * finds free position to save a record on
     * @param $idPlace - ID of place
     * @return free position
     */
    protected function _findPosition($idPlace) {
        $select = $this->select()->where('menu_place_id = ? ',(int)$idPlace)->where('status > -1')->order('position DESC');
        $data = $this->fetchRow($select);
        
        if(!isset($data->position)) {
            return 1;
        } else {
          return ($data->position+1);
        }
    }
}