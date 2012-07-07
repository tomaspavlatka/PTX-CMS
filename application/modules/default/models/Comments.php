<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 9.11.2010
**/

class Default_Model_Comments extends Default_Model_AppModels {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        $this->_dao = new Default_Model_DbTable_Comment();
    }
    
    /**
     * Get 4 Parent.
     * 
     * returns all comments for parent
     * @param $idParent - id of parent
     * @param $parentType - type of parent
     * @param $status - status
     * @return all comments
     */
    public function get4Parent($idParent,$parentType,$status = " = 1") {
        $data = $this->_dao->get4Parent($idArticle,$status);    
        
        if($data instanceof Zend_Db_Table_Rowset) {
            return $data->toArray();
        } else {
            return array();
        }
    }
    
    /**
     * Save.
     * 
     * saves data into database     
     * @param array $formData - data to be saved
     * @param array $params - additional params
     * @return id of inserted record 
     */
    public function save(array $formData, array $params = array()) {
        return $this->_dao->save($formData, $params);
    }
}