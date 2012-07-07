<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 27.11.2010
**/

class Admin_Model_ContentHistories extends Admin_Model_AppModels {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct. 
     * 
     * constructor of calss
     */
    public function __construct() {
        $this->_dao = new Admin_Model_DbTable_ContentHistory();
    }
    
    /**
     * Get history records 
     *
     * @param $idContent - id of content
     * @param $type - type
     * @return data
     */
    public function getHistory($idContent,$type) {
        $data = $this->_dao->getHistory($idContent,$type);    
        
        if($data instanceof Zend_Db_Table_Rowset) {
            $data = $data->toArray();
            foreach($data as &$row) {
                $userObj = new Admin_Model_User($row['user_id']);
                $row['user'] = $userObj->getData()->toArray();
            }
        }
        
        return $data;
    }    
    
    /**
     * save new history record
     * 
     * @param $formData - data for record
     * @param $idRecord - id 
     * @param $idUser - id user
     * @param $type - type of record
     * @return pocet ovliv radku v db
     */
    public function save(array $formData,$idRecord,$idUser,$type) {
        return $this->_dao->save($formData,$idRecord,$idUser,$type);
    }
}