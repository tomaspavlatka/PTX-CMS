<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 24.7.2010
**/

class Default_Model_DbTable_User  extends Default_Model_DbTable_AppModel {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        $this->_name = 'users';
    }
    
    /**
     * Find user by email.
     * 
     * finds user according to his email
     * @param $email - email
     * @return data about user | null
     */
    public function findUserByEmail($email) {
            
        if(!empty($email) && Zend_Validate::is($email,'EmailAddress')) {
            $select = $this->select()->where('status IN (?)',array(1,2))->where('email = ? ',$email);
            return $this->fetchRow($select);
        } else {
            return null;
        }
    }
    
    /**
     * Update password.
     * 
     * updates password for an user     
     * @param string $password - new password
     * @param integer $id - id of user.
     * @return number of affected rows in db
     */
    public function updatePassword($password, $id) {
        $data = array('password' => md5($password.SECURITY_SALT));
        
        $where = $this->getAdapter()->quoteInto('id = ?',(int)$id);       
        $rows = parent::update($data,$where); 
        if($rows > 0) {
            $this->_saveLog($data,$where);
        }
        return (int)$rows;
    }
}