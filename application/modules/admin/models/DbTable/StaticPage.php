<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 22.8.2010
**/

class Admin_Model_DbTable_StaticPage extends Admin_Model_DbTable_AppModel {

    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        
        // Table.
        $this->_name = 'static_pages';
        $this->_transFields = array('name','seo_keywords','seo_description','content','url','url_hash');
    }
    
    /**
     * Find by url hash.
     * 
     * finds all records according to their url hash
     * @param $hash - hash
     * @param $status - status
     * @return records meet find criteria
     */
    public function findByUrlHash($hash,$status) {
        $select = $this->select()->where('url_hash = ?',$hash)->where('status '.$status)->order('id desc');
        return parent::fetchAll($select);
    }
    
    /**
     * Update.
     * 
     * updates data in database
     * @param $formData - updated data to be saved
     * @param $id - ID
     * @return number of affected rows in db
     */
    public function ptxUpdate(array $formData,$id) {
        foreach($formData as $key => $value) {
            if(strstr($key,'content')) {
                $formData[substr($key,1)] = $value;
                unset($formData[$key]);
            }
        }
        
        $data = array(            
            'published'  => $this->_transformDate($formData['published']),
            'status'     => (int)$formData['status']);
        
        // Bind translations.
        $this->_bindTransFields($formData,$data,array('url','url_hash'));
        $this->_bindUrls($data);
        
        $where = $this->getAdapter()->quoteInto('id = ?',(int)$id);
        $rows = parent::update($data,$where); 
        if($rows > 0) {
            $this->_saveLog($data,$where);
        }
        return (int)$rows;
    }
    
    /**
     * Save.
     * 
     * saves new record into database
     * @param $data - data to be saved.     
     * @return id of inserted record in database
     */
    public function save(array $formData) {
        
        foreach($formData as $key => $value) {
            if(strstr($key,'content')) {
                $formData[substr($key,1)] = $value;
                unset($formData[$key]);
            }
        }
        
        $data = array(            
            'published'  => $this->_transformDate($formData['published']),
            'status'     => (int)$formData['status']);
        
        // Bind translations.
        $this->_bindTransFields($formData,$data,array('url','url_hash'));
        $this->_bindUrls($data);
        
        $id = parent::insert($data);
        if(is_numeric($id)) {
            $this->_saveLog($data);
        }
        return (int)$id; 
    }
}