<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 27.11.2010
**/

class Admin_Model_DbTable_ContentHistory extends Admin_Model_DbTable_AppModel {
    
    /************* PUBLIC FUNCTION ***************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        
        $this->_name = 'content_histories';
    }
    
    /**
     * Get history records 
     *
     * @param $idContent - id of content
     * @param $type - type
     * @return data
     */
    public function getHistory($idContent,$type) {
        $select = $this->select()->where('content_id = ?',(int)$idContent)->where('content_type = ?',$type)->order('created desc');
        return parent::fetchAll($select); 
    }  
    
    /**
     * Save.
     * 
     * save new history record
     * @param $formData - data for record
     * @param $idRecord - id 
     * @param $idUser - id user
     * @param $type - type of record
     * @return id of inserted record
     */
    public function save(array $formData,$idRecord,$idUser,$type) {
        
        $data = array(
            'user_id'       => (int)$idUser,
            'content_id'    => (int)$idRecord,
            'content_type'  => $type,         
            'locale'        => $formData['locale'],       
            'perex'         => $formData['perex'],
            'content'       => $formData['content'],
            'notice'        => nl2br($formData['notice']),
            'created'       => time());
        
        $id = parent::insert($data);
        if(is_numeric($id)) {
            $this->_saveLog($data);
        }
        return (int)$id; 
    }
}