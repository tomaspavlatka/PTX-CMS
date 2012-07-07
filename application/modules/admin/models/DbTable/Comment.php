<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 9.11.2010
**/

class Admin_Model_DbTable_Comment extends Admin_Model_DbTable_AppModel {
    
    /************* PUBLIC FUNCTION ***************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        
        $this->_name = 'comments';
    }    
    
    /**
     * Update.
     * 
     * updates data in database
     * @param $formData - data to be updated
     * @param $id - ID
     * @return number of affected rows
     */
    public function ptxUpdate($formData,$id) {
        $data = array(
            'comment_id'   => (!empty($formData['parent'])) ? (int)$formData['parent'] : 0,
            'personname'  => $formData['name'],   
            'message'     => nl2br(trim($formData['message'])));
        
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
     * @param $formData - data to be saved.
     * @param $parentType - type of parent
     * @param $idParent - id of parent
     * @return id of inserted record into db
     */
    public function save($formData,$parentType,$idParent) {
        $data = array(
            'comment_id'   => (!empty($formData['parent'])) ? (int)$formData['parent'] : 0,
            'personname'  => $formData['name'],   
            'message'     => nl2br(trim($formData['message'])),
            'parent_type' => $parentType,
            'parent_id' => (int)$idParent,
            'status' => 1,
            'created' => time());
        
        $id = parent::insert($data);
        if(is_numeric($id)) {
            $this->_saveLog($data);
        }
        return (int)$id;    
    }
}