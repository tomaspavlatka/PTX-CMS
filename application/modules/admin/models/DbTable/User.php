<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 25.7.2010
**/

class Admin_Model_DbTable_User extends Admin_Model_DbTable_AppModel {
    
    /************* PUBLIC FUNCTION ***************/
    /**
     * Construct.
     * 
     * constructor of class.
     */
    public function __construct() {
        parent::__construct();
        
        $this->_name = 'users';
    }
    
    /**
     * Find by Email.
     * 
     * looks for user according to his email
     * @param $email - email
     * @return data from db (fetchAll)
     */
    public function findByEmail($email) {
        $select = $this->select()->where('email = ?',$email);
        return parent::fetchAll($select);
    }
    
    /**
     * Updated.
     * 
     * updates data into database
     * @param $formData - data to be updated
     * @param $id - ID
     * @return number of affected rows in db
     */
    public function ptxUpdate(array $formData, $id) {
        $data = array(
            'role'          => $formData['role'],
            'email'         => $formData['email'],
            'name'          => $formData['personname'],
            'locale'        => $formData['locale'],
            'updated'       => time(),
            'status'        => (int)$formData['status']);
        
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
     * saves data into database
     * @param $formData - data to be saved.     
     * @return id of inserted record in db
     */
    public function save(array $formData) {
        $data = array(
            'role'          => $formData['role'],
            'email'         => $formData['email'],
            'name'          => $formData['personname'],
            'locale'        => $formData['locale'],
            'password'      => md5($formData['password'].SECURITY_SALT),
            'created'       => time(),
            'status'        => (int)$formData['status']);
        
        $id = parent::insert($data);
        if(is_numeric($id)) {
            $this->_saveLog($data);
        }
        return (int)$id; 
    }
    
    /**
     * Update passwd.
     * 
     * updates password for user
     * @param $passwd - password
     * @param $id - ID
     * @return number of affected rows in db
     */
    public function updatePasswd($passwd,$id) {
        $data = array('password' => md5($passwd.SECURITY_SALT));
        
        $where = $this->getAdapter()->quoteInto('id = ?',(int)$id);       
        $rows = parent::update($data,$where); 
        if($rows > 0) {
            $this->_saveLog($data,$where);
        }
        return (int)$rows;
    }
}