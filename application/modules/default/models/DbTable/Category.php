<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 23.8.2010
**/

class Default_Model_DbTable_Category extends Default_Model_DbTable_AppModel {
    
    /************* PUBLIC FUNCTION ***************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        
        $this->_name = 'categories';
    }
    
    /**
     * Find by hash.
     * 
     * finds record in db according to its url hash.
     * @param $hash - url hash
     * @param $status - status
     * @return data from db (fetchRow)
     */
    public function findByHash($hash,$status = " > -1") {
        $select = $this->select()->where('url_hash_'.$this->_locale.' = ?',$hash)->where('status '.$status)->order('id desc');
        return parent::fetchRow($select);
    }
}