<?php

class Default_Plugin_Project extends Zend_Controller_Plugin_Abstract {
    
	/**
	 * Construct.
	 * 
	 * constructor of the class
	 * @param $db
	 */
    public function __construct($db) {
        // Get data from db.
    	$locale = Zend_Registry::get('PTX_Locale');
    	$select = new Zend_Db_Select($db);
    	$select->from('settings')->columns(array('code','value'));
        $stmt = $select->query();
        $settings = $stmt->fetchAll();
        
        // Build list
        $list = array();
        foreach($settings as $key => $values) {
        	$list[$values['code']] = $values['value'];
        }
        
        // Store into Session.
        PTX_Session::setSessionValue('settings', 'project', $list);
    }
} 