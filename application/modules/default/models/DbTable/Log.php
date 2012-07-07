<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 26.7.2010
**/

class Default_Model_DbTable_Log extends Default_Model_DbTable_AppModel {
    
    /************* PUBLIC FUNCTION *************/
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
     * Save.
     * 
     * saves new record in database
     * @param $idUser - ID of user
     * @param $table - table
     * @param $where - where
     * @param $content - content
     * @return id of inserted record
     */
    public function save($idUser,$table,$where,$content) {
        $data = array(
            'user_id'       => (int)$idUser,
            'log_table'     => $table,
            'log_where'     => $where,
            'log_content'   => $content,
            'created'       => time());
        
        return $this->insert($data);
    }
}