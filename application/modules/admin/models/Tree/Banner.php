<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Sep 23, 2011
 */
  
class Admin_Model_Tree_Banner extends Admin_Model_Tree_AppModel {
    
    /**
     * Construct.
     * 
     * constructor of class 
     */
    public function __construct() {
        parent::__construct();
        $this->_name = 'banners'; 
        $this->_transFields = array('name','title','link');
    }
}