<?php
/**
 * Author: Tomas Pavlatka
 * Email: tomas.pavlatka@gmail.com
 * Created: 19.1.2010 8:23:49
 */
 
class Admin_Plugin_Navigation extends Zend_Controller_Plugin_Abstract {
    
	/************* VARIABLES ****************/
    private $_acl;
	private $_role;
	private $_session;
    private $_locale;
    
	/************* PUBLIC FUNCTION ****************/
    /**
     * konstruktor
     * @param Model_LibraryAcl $acl - acl
     */
	public function __construct(Default_Model_LibraryAcl $acl) {
		// nastavime promenne
		$this->_setVariables($acl);

		// pripravime si pole
		$array = $this->_getArray();
			
		// pripravi a ulozi navigaci do pole
		$this->_prepareNavigation($array);
    }
    
    /************* PRIVATE FUNCTION ****************/
    /**
     * prevede xml na pole
     */
    private function _getArray() {
    	$treeObj = new Admin_Model_Tree_AdminMenu();
    	$params = array('mandatory' => array('name'));
    	return $treeObj->getTree(0,0," = 1",$params);
    }
    
    /**
     * pripravi navigaci pro daneho uzivatele
     * @param array $array - pole z db
     */
    private function _prepareNavigation(array $array) {
        $session = new Zend_Session_Namespace('admindata');
        $sessionArray = array();
        
        $allowed = array();
        foreach($array as $row) {
            $ident = $row['module'].':'.$row['controller'].':'.$row['action'];
            
            $counter = 1;
            while(array_key_exists($ident,$sessionArray)) {
                $ident .= '-'.$counter++;
            }
            
            if($row['level'] == 0) {
                if($this->_acl->isAllowed($this->_role,$row['module'].':'.$row['controller'],$row['action'])) {
                    $sessionArray[$ident] = $row;
                    $allowed[] = $row['id'];
                }
            } else {
                if(in_array($row['parent_id'],$allowed) && $this->_acl->isAllowed($this->_role,$row['module'].':'.$row['controller'],$row['action'])) {                    
                    $sessionArray[$ident] = $row;
                    $allowed[] = $row['id'];
                }
            }
        }

        $session->navigation = $sessionArray;        
    }
    
    /************* PRIVATE FUNCTION *************/
    
    /**
     * nastavi lokalni promenne
     * @param unknown_type $acl - acl
     */
    private function _setVariables($acl) {
        
    	$this->_acl = $acl;
    	$this->_role = Zend_Registry::get('role');
    	$this->_session = new Zend_Session_Namespace('admindata');    
    	$this->_locale = Zend_Registry::get('PTX_Locale');	
    }
}