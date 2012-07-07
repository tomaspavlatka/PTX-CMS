<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 27.11.2010
**/

class Admin_Model_ContentHistory extends Admin_Model_AppModel {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     * @param $id - ID
     */
    public function __construct($id) {
        $this->_dao = new Admin_Model_DbTable_ContentHistory();
        $this->_id = (int)$id;
    }

    
    /**
     * Store activate info.
     * 
     * stores information about activate revision
     * @param $notice - notice
     * @return id of inserted record
     */
    public function storeActivateInfo($notice) {
        $data = $this->_data->toArray();
        $data['notice'] = $notice;
        
        // Save data to db
        return $this->_dao->save($data,$this->_data->content_id,$this->_data->user_id,$this->_data->content_type);
    }
}