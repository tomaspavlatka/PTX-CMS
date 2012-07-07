<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Oct 22, 2011
 */
 
class Admin_Model_DbTable_Location extends Admin_Model_DbTable_AppModel {
    
    /************* PUBLIC FUNCTION ***************/
    /**
     * Constructor.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        $this->_name = 'locations';
        $this->_transFields = array('name');
    }
    
    /** 
     * Find for position change.
     * 
     * find record in db suitable for change position
     * @param $idParent - ID of parent
     * @param $position - position
     * @return data from db      
     */
    public function find4PositionChange($idParent,$position,$way) {
        $select = $this->select();
        $select->where('parent_id = ? ',(int)$idParent)->where('status > -1');
        
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
     * update data in database
     * @param $formData - data to be updated
     * @param $id - ID
     * @param $updPosition - position should be updated 
     * @param $parentType - parent type for category
     * @return number of affected rows in db
     */
    public function ptxUpdate(array $formData, $id,$updPosition,$parentType) {
        $data = array(
            'parent_id' => (int)$formData['parent'],
            'updated'   => time(),
            'status'    => (int)$formData['status']);
            
        if($updPosition) {
            $data['position'] = $this->_findPosition($formData['parent'],$parentType);
        }
        
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
     * @param array $formData - data to be saved.     
     * @param array $params - additional params 
     * @return id of inserted record
     */
    public function save(array $formData,array $params = array()) {
        $data = array(
            'parent_id'           => (int)$formData['parent'],
            'position'            => $this->_findPosition($formData['parent']),
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
    
    /**
     * Find position.
     * 
     * find free position for save
     * @param $idParent - ID parent
     * @return free position
     */
    protected function _findPosition($idParent) {
        $select = $this->select()->where('parent_id = ? ',(int)$idParent)->where('status > -1')->order('position DESC');
        $data = $this->fetchRow($select);
        
        if(!isset($data->position)) {
            return 1;
        } else {
          return ($data->position+1);
        }
    }
}