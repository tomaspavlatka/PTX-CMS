<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 25.7.2010
**/

class Admin_Model_DbTable_Message extends Admin_Model_DbTable_AppModel {
    
    /************* PUBLIC FUNCTION ***************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        
        $this->_name = 'messages';
    }
    
    /**
     * Get neighbor message
     * 
     * returns message which is next or previous
     * @param $type - next | previous
     * @param $idUserTo - ID of user for who a message is
     * @param $timeLimit - time of neighbor message
     * @return data about message
     */
    public function getNeighborMessage($type,$idUserTo, $timeLimit) {
        $select = $this->select()->where('status = 1')->where('user_id_to = ?',(int)$idUserTo)->limit(1);
        
        if($type == 'next') {
            $select->where('created > ?',(int)$timeLimit)->order('created desc');
        } else {
            $select->where('created < ?',(int)$timeLimit)->order('created asc');
        }
        
        return parent::fetchRow($select);
    }
    
    /**
     * Mark as read.
     * 
     * marks message as read
     * @param $id - ID
     * @return number of affected rows in db
     */
    public function markAsRead($id) {
        $data = array('read'=>time());
        
        $where = $this->getAdapter()->quoteInto('id = ?',(int)$id);       
        $rows = parent::update($data,$where); 
        if($rows > 0) {
            $this->_saveLog($data,$where);
        }
        return (int)$rows;
    }
    
     /**
     * Update.
     * 
     * updates data in database
     * @param $formData - data to be updated
     * @param $idUser - ID
     * @return number of affected rows in db
     */
    public function ptxUpdate(array $formData, $idUser) {
        $data = array(
            'subject'       => $formData['subject'],
            'user_id_from'       => (int)$idUser,
            'user_id_to'    => (int)$formData['to'],
            'message'       => nl2br($formData['message']));
        
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
     * save new message into db
     * @param $data - data to be saved data
     * @param $idUser - ID of user who is sending message     
     * @return id of inserted record in database
     */
    public function save(array $formData,$idUser) {
        $data = array(
            'subject'       => $formData['subject'],
            'user_id_from'       => (int)$idUser,
            'user_id_to'    => (int)$formData['to'],
            'message'       => nl2br($formData['message']),
            'created'       => time(),
            'status'        => 1);
        
        $id = parent::insert($data);
        if(is_numeric($id)) {
            $this->_saveLog($data);
        }
        return (int)$id; 
    }
}