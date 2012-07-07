<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 12, 2010
**/

class Admin_Model_UrlBackups extends Admin_Model_AppModels {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        $this->_dao = new Admin_Model_DbTable_UrlBackup();
    }
    
    /**
     * Save.
     * 
     * save new record into database
     * @param $idContent - id of content
     * @param $urlHash - url hash
     * @param $contentType - type of content
     * @param $locale - locale
     * @return id of inserted record
     */
    public function save($idContent,$contentType,$url,$locale) {
        return $this->_dao->save($idContent,$contentType,$url,$locale);
    }
}