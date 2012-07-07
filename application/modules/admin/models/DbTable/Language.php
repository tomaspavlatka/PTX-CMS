<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Oct 13, 2011
 */
  
class Admin_Model_DbTable_Language extends Admin_Model_DbTable_AppModel {
    
    /************* PUBLIC FUNCTION ***************/
    /**
     * Constructor.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        $this->_name = 'languages';
        $this->_transFields = array('name');
    }
    
    /** 
     * Find for position change.
     * 
     * find record in db suitable for change position
     * @param $idParent - ID of parent
     * @param $position - position
     * @param $way - way
     * @param $parentType - parent type for category
     * @return data from db      
     */
    public function find4PositionChange($idParent,$position,$way,$parentType) {
        $select = $this->select()->where('status > -1');
        
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
     * Find by Code.
     * 
     * finds record according to code.
     * @param string $code - code
     * @param string $status - restrict to proper status
     * @param boolean $inArray - result should be returned as array 
     * @return data
     */
    public function findByCode($code,$status,$inArray = false) {
        $select = $this->select()->where('code = ?',$code)->where('status '.$status);
        $data = $this->fetchRow($select);
        
        if($inArray) {
        	if($data instanceof Zend_Db_Table_Row) {
        		$data = $data->toArray();
        	} else {
        		$data = array();
        	}
        }
        
        return $data;
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
            'code'         => $formData['code'],
            'locale'       => $formData['locale'],
            'charset'      => $formData['charset'],
            'updated'      => time(),
            'status'       => $formData['status']);
        
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
     * @param $filename - filename with picture 
     * @param $parentType - parent type for category    
     * @return id of inserted record
     */
    public function save(array $formData) {
        $data = array(
            'code'         => $formData['code'],
            'locale'       => $formData['locale'],
            'charset'      => $formData['charset'],
            'created'      => time(),
            'updated'      => time(),
            'position'     => $this->_findPosition(),
            'status'       => $formData['status']);
        
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
     * @return free position
     */
    protected function _findPosition() {
        $select = $this->select()->where('status > -1')->order('position DESC');
        $data = $this->fetchRow($select);
        
        if(!isset($data->position)) {
            return 1;
        } else {
          return ($data->position+1);
        }
    }
}