<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 22.8.2010
**/


class Default_Model_StaticPages extends Default_Model_AppModels {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        $this->_dao = new Default_Model_DbTable_StaticPage();
    }
    
    /**
     * Find by url hash.
     * 
     * finds record according to its url hash
     * @param $hash - url hash
     * @param $status - status
     * @return data about page | null
     */
    public function findByUrlHash($urlHash,$status) {
        $data = $this->_dao->findByUrlHash($urlHash,$status);
        
        if($data instanceof Zend_Db_Table_Rowset) {
            $data = $data->toArray();
            if(isset($data[0]['id'])) {
                return $data[0];
            } else {
                return null;
            }
        } else {
            return null;
        }
    }    
}