<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 9, 2010
**/

class Admin_Model_DbTable_Widget extends Admin_Model_DbTable_AppModel {
    
    /************* PUBLIC FUNCTION ***************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        
        // Table.
        $this->_name = 'widgets';
        $this->_transFields = array('name','content');
    }
    
    /** 
     * Find for position change.
     * 
     * find record in db suitable for change position
     * @param $idPlace - ID of place
     * @param $idUser - ID of user
     * @param $position - position
     * @param $way - way
     * @return data from db      
     */
    public function find4PositionChange($idPlace,$idUser,$position,$way) {
        $select = $this->select();
        $select->where('widget_place_id = ?',(int)$idPlace)->where('status > -1')->where('user_id = ?',(int)$idUser);
        
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
     * @param array $formData - data to be updated
     * @param integer $id - id o record
     * @param array $params - additions params.
     * @return number of affected rows in db
     */
    public function ptxUpdate($formData,$id, $params) {
        $data = array(
            'updated'   => time(),
            'show_name' => (isset($formData['showname'])) ? (int)$formData['showname'] : 0,
            'status'    => (isset($formData['status'])) ? (int)$formData['status'] : 1);
            
        foreach($formData as $key => $values) {
            if(strstr($key,'content')) {
                $formData[substr($key,1)] = $values;
                unset($formData[$key]);
            } 
        }
        $this->_bindTransFields($formData,$data);
        $data = $this->_completeArray($formData,$data,$params);
        
        $where = $this->getAdapter()->quoteInto('id = ?',(int)$id);   
        $row = parent::update($data,$where);
        if($row > 0) {
            $this->_saveLog($data,$where);
        }
        return (int)$row; 
    }
    
    /**
     * Save.
     * 
     * saves new record into database
     * @param array $data - data to be saved
     * @param array $params - additional params
     * @return id of inserted record in database
     */
    public function save(array $formData, array $params = array()) {
        $data = array(
            'user_id'          => (int)$params['user_id'],
            'widget_place_id'  => (int)$params['place'],
            'parent_type'      => $params['parent_type'],
            'position'         => $this->_findPosition($params['place'],$params['user_id']),
            'show_name'        => (isset($formData['showname'])) ? (int)$formData['showname'] : 0,
            'created'          => time(),
            'updated'          => time(),
            'status'           => (isset($formData['status'])) ? (int)$formData['status'] : 1);
        
        foreach($formData as $key => $values) {
            if(strstr($key,'content')) {
                $formData[substr($key,1)] = $values;
                unset($formData[$key]);
            } 
        }
        $this->_bindTransFields($formData,$data);
        $data = $this->_completeArray($formData,$data,$params);
        
        $id = parent::insert($data);
        if(is_numeric($id)) {
            $this->_saveLog($data);
        }
        return (int)$id; 
    }

    /**
     * Complete array.
     * 
     * completes array for record
     * @param array $formData - data from form
     * @param array $data - data to be used 
     * @param array $params - additional params
     * @return array with data for further usage
     */
    protected function _completeArray($formData,$data,$params) {        
        $type = $params['parent_type'];
        
        if(in_array($type,array('articlelast','articlerandom','articleshown','twitter'))) {
            $param = 'number='.(int)$formData['number'];
            $cats = null;
            
            if(isset($formData['category'])) {
                foreach($formData['category'] as $ident => $value) {
                    if(!empty($value)) {
                        if(!empty($cats)) {
                            $cats .= '#';
                        }
                        
                        $cats .= (int)$value;
                    }
                }
            }
            
            if(!empty($cats)) {
                $param .= '|~|cats='.$cats;
            }
            $data['param'] = $param;
        } else if($type == 'banner') {
            $param = 'number='.(int)$formData['number'];
            $cats = null;
            
            if(isset($formData['category'])) {
                foreach($formData['category'] as $ident => $value) {
                    if(!empty($value)) {
                        if(!empty($cats)) {
                            $cats .= '#';
                        }
                        
                        $cats .= (int)$value;
                    }
                }
            }
            
            if(!empty($cats)) {
                $param .= '|~|cats='.$cats;
            }
            $param .= '|~|shuffle='.$formData['random'];
            $data['param'] = $param;
        }       
        
        return $data;
    }
    
    /**
     * Find position.
     * 
     * finds free position for new widget
     * @param $idPlace - ID of widget place
     * @param $idUser - ID of user
     * @return free position
     */
    protected function _findPosition($place,$idUser) {
        $select = $this->select()->where('widget_place_id = ? ',(int)$place)->where('status > -1')->where('user_id = ?',(int)$idUser)->order('position DESC');
        $data = $this->fetchRow($select);
        
        if(!isset($data->position)) {
            return 1;
        } else {
          return ($data->position+1);
        }
    }
}