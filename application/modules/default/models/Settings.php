<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Oct 1, 2011
 */
  
class Default_Model_Settings extends Default_Model_AppModels {
    
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        $this->_dao = new Default_Model_DbTable_Setting();
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
}