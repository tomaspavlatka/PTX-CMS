<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Oct 1, 2011
 */
  
class Admin_Model_DbTable_Setting extends Admin_Model_DbTable_AppModel {
    
    /************* PUBLIC FUNCTION ***************/
    /**
     * Constructor.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        $this->_name = 'settings';
    }
    
    /**
     * Code Exists.
     * 
     * checks whether code exists or not.
     * @param string $code - code
     * @return true | false
     */
    public function codeExists($code) {
    	$select = $this->select()->where('code = ?',$code);
    	$result = parent::fetchAll($select);
    	
    	if($result instanceof Zend_Db_Table_Rowset) {
            $result = $result->toArray();
            if(!empty($result)) {
            	return true;
            } else {
            	return false;
            }
    	} else {
    		return false;
    	}
    }
    
    /**
     * Get All.
     * 
     * returns all records from database.
     * @return all records
     */
    public function getAll() {
    	$select = $this->select();
        return parent::fetchAll($select);
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
            'name'                => $formData['name'],
            'updated'             => time());
        
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
            'code'       => $formData['code'],
            'created'    => time(),
            'updated'    => time(),
            'value'      => $formData['value']);
        
        $id = parent::insert($data);
        if(is_numeric($id)) {
            $this->_saveLog($data);
        }
        return (int)$id; 
    }
    
    /**
     * Update code.
     * 
     * updates data in database for code
     * @param array $data - data to be saved
     * @param string $code - code
     * @return num of affected rows in db
     */
    public function updateCode(array $data, $code) {
        $where = $this->getAdapter()->quoteInto('code = ?',$code);
        $rows = parent::update($data,$where); 
        if($rows > 0) {
            $this->_saveLog($data,$where);
        }
        return (int)$rows;
    }
}