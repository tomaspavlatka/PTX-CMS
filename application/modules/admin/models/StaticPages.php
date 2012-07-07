<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 22.8.2010
**/

class Admin_Model_StaticPages extends Admin_Model_AppModels {
   
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Contstruct.
     * 
     * constructor of class
     */
    public function __construct() {
        $this->_dao = new Admin_Model_DbTable_StaticPage();
    }

    /**
     * Name in use.
     * 
     * checks whether name exists in database
     * @param $name - name
     * @param $excludeId - exclude this id from checking (update)
     * @return true | false
     */
    public function nameInUse($name,$excludeId = null) {
        $urlHash = md5(PTX_Uri::getUri($name));
        $data = $this->_dao->findByUrlHash($urlHash," > -1");
        
        if($data instanceof Zend_Db_Table_Rowset) {
            $arrayData = $data->toArray();
            return (!isset($arrayData[0]['id']) || $arrayData[0]['id'] == $excludeId) ? false : true; 
        } else {
            return false;
        }
    }    
}
