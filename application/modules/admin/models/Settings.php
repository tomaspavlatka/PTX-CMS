<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Oct 1, 2011
 */
  
class Admin_Model_Settings extends Admin_Model_AppModels {
    
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        $this->_dao = new Admin_Model_DbTable_Setting();
    }
    
    /**
     * Get Settings.
     * 
     * returns array with actual settings.
     * @param string $return - what we want to receive
     * @return array with settings.
     */
    public function getSettings($return = 'list') {
    	$settings = $this->_dao->getAll();
    	
    	if($return == 'object') {
    		return $settings;
    	} else if($return == 'list') {
    		$list = array();
    		if($settings instanceof Zend_Db_Table_Rowset) {
    			$settings = $settings->toArray();
	    		foreach($settings as $key => $values) {
	                $list[$values['code']] = $values['value'];
	            }
    		}
    		
    		return (array)$list;
    	}
    }
    
    /**
     * Save Array.
     * 
     * saves all parameters from an array
     * @param array $data - array with data to be saved.
     */
    public function saveArray(array $data) {
    	foreach($data as $key => $value) {
    		$saveData = array(
                'code'  => $key,
    		    'value' => $value,
    		);
    		if($this->_dao->codeExists($key)) {                
    			$this->_dao->updateCode($saveData, $key);
    		} else {
    			$this->_dao->save($saveData);
    		}
    	}
    }
}