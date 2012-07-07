<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 10.8.2010
**/

include_once APPLICATION_PATH.'/helpers/Trans.php';

class Admin_View_Helper_WidgetTypeName {

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
    public function widgetTypeName($ident) {
        switch($ident) {
            case 'articlelast':
                return $this->_trans->trans('Last articles');
            case 'articlerandom':
                return $this->_trans->trans('Random articles');
            case 'articleshown':
                return $this->_trans->trans('Top shown articles');
            case 'banner':
                return $this->_trans->trans('Banner section');
            case 'separator':
                return $this->_trans->trans('Separator');
            case 'shortcut':
                return $this->_trans->trans('Shortcut');
            case 'text':
                return $this->_trans->trans('Text');
            case 'twitter':
                return $this->_trans->trans('Twitter');
        }
    }   
}