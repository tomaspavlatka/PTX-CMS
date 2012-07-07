<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 26.7.2010
**/

class Default_Model_Logs extends Default_Model_AppModel {

    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class.
     */
    public function __construct() {
        $this->_dao = new Default_Model_DbTable_Log();
    }
    
    /**
     * Save.
     * 
     * saves new record into database
     * @param $idUser - ID of user
     * @param $table - table
     * @param $where - where
     * @param $content - content
     * @return id of inserted record
     */
    public function save($idUser,$table,$where,$content) {
        return $this->_dao->save($idUser,$table,$where,$content);
    }
}