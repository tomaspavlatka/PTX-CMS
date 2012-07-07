<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 25.7.2010
**/

class Admin_Model_Messages extends Admin_Model_AppModels {
    
    /************* PUBLIC FUNCTION *************/
    
    /**
     * Construct.
     * 
     * constructor of class.
     */
    public function __construct() {
        $this->_dao = new Admin_Model_DbTable_Message();
    }
    
    
    /**
     * Save.
     * 
     * save new message into db
     * @param $data - data to be saved data
     * @param $idUser - ID of user who is sending message     
     * @return id of inserted record in database
     */
    public function save(array $formData,$idUser) {
        return $this->_dao->save($formData,$idUser);
    }
}