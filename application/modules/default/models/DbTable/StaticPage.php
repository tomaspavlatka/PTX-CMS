<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 22.8.2010
**/

class Default_Model_DbTable_StaticPage extends Default_Model_DbTable_AppModel {
    
    /************* VARIABLES *************/
    protected $_name = 'static_pages';
    
    /************* PUBLIC FUNCTION ***************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Find by url hash.
     * 
     * finds records according to their url hash
     * @param $hash - url hash
     * @param $status - status
     * @return records from db (fetchAll)
     */
    public function findByUrlHash($hash,$status) {
        $select = $this->select()->where('url_hash_'.$this->_locale.' = ?',$hash)->where('status '.$status)->order('id desc');
        return parent::fetchAll($select);
    }
    
    /**
     * Update shown.
     * 
     * updates how many time a record has been shown 
     * @param $value - new valu
     * @param $id - ID
     * @return number of affected rows in db
     */
    public function updateShown($value,$id) {
        $data = array('shown' => (int)$value);
        
        $where = $this->getAdapter()->quoteInto('id = ?',(int)$id);     
        $rows = parent::update($data,$where); 
        if($rows > 0) {
            $this->_saveLog($data,$where);
        }
        return (int)$rows;
    }
}