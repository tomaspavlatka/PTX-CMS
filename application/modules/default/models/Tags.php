<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 18, 2010
**/

class Default_Model_Tags extends Default_Model_AppModels {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class.
     */
    public function __construct() {
        $this->_dao = new Default_Model_DbTable_Tag();
    }
    
    /**
     * Find by url hash.
     * 
     * finds tag according to its url hash
     * @param $urlHash - url hash
     * @param $status - status
     * @return id of tag | null     
     */
    public function findByUrlHash($urlHash, $status = " > -1") {
        $data = $this->_dao->findByUrlHash($urlHash, $status);
        
        if($data instanceof Zend_Db_Table_Rowset_Abstract && isset($data[0]) && isset($data['0']->id)) {
            return $data['0']->id;
        } else {
            return null;
        }
    } 
}