<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Oct 12, 2011
 */
  
class Admin_Model_DbTable_Log extends Admin_Model_DbTable_AppModel {
    
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        
        $this->_name = '_logs';
    }        
    
    /**
     * Delete IDs.
     * 
     * deletes all records from logs with ids 
     * @param array $ids - array with ids to be deleted
     */
    public function deleteIds(array $ids) {
    	if(!empty($ids)) {
	        $where = 'id IN ('.implode(',',$ids).')';
	        return $this->delete($where);
    	}	
    }
    
    /**
     * Get Older than.
     * 
     * returns logs older than time limit
     * @param integer $timelimit - time limit
     * @param boolean $inArray - return result as array ?
     * @return data from db.
     */
    public function getOlderThen($timelimit, $inArray = false) {
    	$select = $this->select();
    	$select->where('created < ?',(int)$timelimit)->order('created ASC');
    	$data = $this->fetchAll($select);
    	
    	if($inArray && ($data instanceof Zend_Db_Table_Rowset)) {
    		return (array)$data->toArray();
    	} else {
    	    return $data;
    	}
    }
}
