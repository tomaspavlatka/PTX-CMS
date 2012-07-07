<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 22.8.2010
**/

class Admin_Model_DbTable_UrlBackup extends Admin_Model_DbTable_AppModel {

    /************* PUBLIC FUNCTION ***************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        
        $this->_name = 'url_backups';
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
    public function save($idContent,$contentType,$url, $locale) {
        $data = array(
            'parent_id'     => (int)$idContent,
            'parent_type'   => $contentType,
            'locale'        => $locale,
            'url_hash'      => $url);
        
        $id = parent::insert($data);
        if(is_numeric($id)) {
            $this->_saveLog($data);
        }
        return (int)$id;
    }
}