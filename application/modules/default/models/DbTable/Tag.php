<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 18, 2010
**/

class Default_Model_DbTable_Tag extends Default_Model_DbTable_AppModel {
    
    /************* PUBLIC FUNCTION ***************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        $this->_name = 'tags';
    }
    
    /**
     * Find by url hash.
     * 
     * finds tag according to its url hash
     * @param $urlHash - url hash
     * @param $status - status
     * @return data from db (fetchAll)     
     */
    public function findByUrlHash($urlHash, $status = " > -1") {
        $select = $this->select()->where('url_hash_'.$this->_locale.' = ?',$urlHash)->where('status '.$status);
        return parent::fetchAll($select);
    } 
}