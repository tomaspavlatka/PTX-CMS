<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 25.7.2010
**/

include_once APPLICATION_PATH.'/helpers/Trans.php';

class Admin_View_Helper_RoleName {

    /*************** VARIABLES ****************/
    private $_trans;
    
    /*************** PUBLIC FUNCTION ****************/
    /**
     * konstruktor     
     */
    public function __construct() {
        $this->_trans = new Zend_View_Helper_Trans;
    }
    
    /**
     * vrati nazev role
     * @param $ident - identificator
     * @return $role     
     */
    public function roleName($ident) {
        switch($ident) {
            case 'guests':
                return $this->_trans->trans('Guest');
            case 'users':
                return $this->_trans->trans('User');
            case 'admins':
                return $this->_trans->trans('Admin');
            case 'sadmins':
                return $this->_trans->trans('Super Admin');
            default:
            	return $ident;
        }
    }   
}