<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Oct 6, 2011
 */
  
class Admin_Model_DbTable_Replacer extends Admin_Model_DbTable_AppModel {
    
    /************* PUBLIC FUNCTION ***************/
    /**
     * Constructor.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        $this->_name = 'replacers';
        $this->_transFields = array('content');
    }
    
    /**
     * Code exists.
     * 
     * checks whether record with code exists or not
     * @param string $code - code to be checked
     * @param integer $exclude - id to be excluded from search
     * @return true | false
     */
    public function codeExists($code, $exclude = null) {
        $select = $this->select()->where('code = ?',$code)->where('status > -1');
        if(!empty($exclude)) {
        	$select->where('id != ?',(int)$exclude);
        }
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
            'code'         => $formData['code'],
            'script'       => $formData['script'],
            'updated'      => time(),
            'status'       => $formData['status']);
        
        foreach($formData as $key => $value) {
            if(strstr($key,'content')) {
                $formData[substr($key,1)] = $value;
                unset($formData[$key]);
            }
        }
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
            'code'         => $formData['code'],
            'script'       => $formData['script'],
            'created'      => time(),
            'updated'      => time(),
            'status'       => $formData['status']);
        
        foreach($formData as $key => $value) {
            if(strstr($key,'content')) {
                $formData[substr($key,1)] = $value;
                unset($formData[$key]);
            }
        }
        $this->_bindTransFields($formData,$data);
        
        $id = parent::insert($data);
        if(is_numeric($id)) {
            $this->_saveLog($data);
        }
        return (int)$id; 
    }    
}