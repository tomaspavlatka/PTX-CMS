<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Oct 6, 2011
 */
  
class Admin_Model_Replacers extends Admin_Model_AppModels {
    
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        $this->_dao = new Admin_Model_DbTable_Replacer();
    }    
    
    /**
     * Code exists.
     * 
     * checks whether record with code exists or not
     * @param string $code - code to be checked
     * @param integer $exclude - id to be excluded from search
     * @return true | false
     */
    public function codeExists($code, $exclude = null) {
    	return $this->_dao->codeExists($code,$exclude);
    }
}