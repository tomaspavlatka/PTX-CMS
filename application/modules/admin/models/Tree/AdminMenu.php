<?php
/**
 * Author: Tomas Pavlatka
 * Email: tomas.pavlatka@gmail.com
 * Created: 1.2.2010 22:42:07
 */

class Admin_Model_Tree_AdminMenu extends Admin_Model_Tree_AppModel {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class 
     */
    public function __construct() {
        parent::__construct();
        $this->_name = 'admin_menus'; 
        $this->_transFields = array('name','description');
    }
}