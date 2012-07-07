<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 23.8.2010
**/

class Admin_Model_DbTable_Article extends Admin_Model_DbTable_AppModel {

    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        
        $this->_name = 'articles';
        $this->_transFields = array('name','seo_keywords','perex','seo_description','content','url','url_hash','short_name');
    }
    
    /**
     * Update.
     * 
     * updates data in database
     * @param $formData - data to be updated
     * @param $id - ID
     * @return number of affected rows in db
     */
    public function ptxUpdate(array $formData, $id) {
        $data = array(            
            'published'   => $this->_transformDate($formData['published']),
            'updated'     => time(),
            'status'      => (int)$formData['status']);
        
        foreach($formData as $key => $value) {
            if(strstr($key,'content')) {
                $formData[substr($key,1)] = $value;
                unset($formData[$key]);
            }
        }
        
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
     * saves record into database
     * @param $formData - data to be saved     
     * @return id of inserted record in database
     */
    public function save(array $formData) {
        $data = array(            
            'published'  => $this->_transformDate($formData['published']),
            'shown'      => 0,
            'created'    => time(),
            'updated'    => time(),
            'status'     => (int)$formData['status']);
        
        foreach($formData as $key => $value) {
            if(strstr($key,'content')) {
                $formData[substr($key,1)] = $value;
                unset($formData[$key]);
            }
        }
        
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