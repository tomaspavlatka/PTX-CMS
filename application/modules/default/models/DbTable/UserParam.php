<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 24.7.2010
**/

class Default_Model_DbTable_UserParam extends Default_Model_DbTable_AppModel {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        
        $this->_name = 'user_params';
    }
    
    /**
     * Update last login.
     * 
     * updates information about last login.
     * @param $id - ID
     * @return id of inserted record in database
     */
    public function updateLastLogin($id) {
        $data = array(
            'param'   => 'lastlogin',
            'value'   => time(),
            'user_id' => (int)$id,
            'notice'  => $_SERVER['REMOTE_ADDR']);
        
        return parent::insert($data);
    }
}