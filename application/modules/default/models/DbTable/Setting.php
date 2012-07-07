<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Oct 1, 2011
 */
  
class Default_Model_DbTable_Setting extends Default_Model_DbTable_AppModel {
    
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
}