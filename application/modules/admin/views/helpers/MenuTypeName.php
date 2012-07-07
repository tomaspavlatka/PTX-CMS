<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 7.9.2010
**/

include_once APPLICATION_PATH.'/helpers/Trans.php';

class Admin_View_Helper_MenuTypeName {

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
    public function menuTypeName($ident,$parent = null) {
        switch($ident) {
            case 'article':
                return $this->_trans->trans('Article Link');
            case 'category':
                if($parent == 'articles') {
                    return $this->_trans->trans('Article Category Link');
                } else if($parent == 'photos') {
                    return $this->_trans->trans('Photo Category Link');
                }
                break;
            case 'homepage':
                return $this->_trans->trans('Homepage Link');
            case 'link':
                return $this->_trans->trans('External link');
            case 'photogallery':
                return $this->_trans->trans('Photogallery Link');
            case 'staticpage':
                return $this->_trans->trans('Static page Link');                
        }
    }   
}