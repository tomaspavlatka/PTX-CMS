<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 9.11.2010
**/

class Default_Model_DbTable_Comment extends Default_Model_DbTable_AppModel {
    
    /************* PUBLIC FUNCTION ***************/
    /**
     * Contruct.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        
        $this->_name = 'comments';
    }    
    
    /**
     * Get for parent.
     * 
     * returns data for parent record
     * @param $idParent - id parent
     * @param $parentType - type of parent
     * @param $status - status
     * @return comments (fetchAll)
     */
    public function get4Article($idParent,$parentType,$status = " = 1") {
        $select = $this->select()
            ->where('parent_id = ?',(int)$idParent)->where('parent_type = ?',$parentType)
            ->where('status '.$status)->order('created desc');
        return parent::fetchAll($select);    
    }
    
    /**
     * Save.
     * 
     * saves new record into database
     * @param array $formData - form to be saved
     * @param array $params - additional params
     * @return id of inserted row in db
     */
    public function save($formData,array $params = array()) {
        $data = array(
            'parent_id'     => (int)$params['parent_id'],
            'parent_type'   => $params['parent_type'],
            'comment_id'    => (!empty($formData['parent'])) ? (int)$formData['parent'] : 0,
            'name'          => $formData['name'],   
            'message'       => $formData['message'],
            'website'       => $formData['website'],
            'email'         => $formData['email'],
            'code'          => $params['approval_code'],
            'akismet'       => (int)$params['akismet'],
            'created'       => time(),
            'updated'       => time(),
            'ip'            => $_SERVER['REMOTE_ADDR'],
            'status'        => 2);

        $id = parent::insert($data);
        if(is_numeric($id)) {
            $this->_saveLog($data);
        }
        return (int)$id;
    }
}