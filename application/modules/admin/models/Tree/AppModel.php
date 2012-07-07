<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 5, 2010
**/

class Admin_Model_Tree_AppModel extends Zend_Db_Table {
    
    /*
     * Locale (Protected).
     */
    protected $_locale;
    
    /*
     * Tree (Protected).
     */
	protected $_tree = array();
	
	/*
	 * Path (Protected).
	 */
    protected $_path = array();
    
    /*
     * Name (Protected).
     */
    protected $_name;
    
    /*
     * TransFields (Protected).
     */
    protected $_transFields = array();
    
    /**
     * Constructor
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct(); 
        $this->_locale = Zend_Registry::get('PTX_Locale');
    }
    
    /**
     * Get Tree
     * 
     * return tree according to parameters
     * @param integer $parentId - ID of parent
     * @param integer $level - level
     * @param string $status - status
     * @param array $params - additional params
     * @return tree
     */
    public function getTree($parentId,$level,$status, array $params = array()) {
        $select = $this->select()->where('parent_id = ? ',(int)$parentId)->where('status '.$status)->order('position ASC');
        $this->_completeSelect($select,$params);        
        $result = $this->fetchAll($select);
        
        if(!empty($this->_transFields)) {
        	$locale = Zend_Registry::get('PTX_Locale');
        }
        
        foreach($result as $row){
            $array = $row->toArray();
            $array['level'] = $level;

            if(!empty($this->_transFields)) {
            	foreach($this->_transFields as $key => $name) {
                    $array[$name] = $array[$name.'_'.$locale];		
            	}
            }
            
            $this->_tree[] = $array;
            $this->getTree($row->id,$level+1,$status,$params);
        }
        
        return $this->_tree;
    }
    
    /**
     * Get top parent.
     * 
     * return top parent for specific record
     * @param $id - ID 
     * @return top parent
     */
    public function getTopParent($id) {
        $select = $this->select()->where('id = ? ',(int)$id);
        $result = $this->fetchRow($select);
        
        if(isset($result['parent_id']) && $result['parent_id'] == 0) {
            return (array)$result->toArray();             
        } else {
            return $this->getTopParent($result['parent_id']);
        }
    }
    
    /**
     * Get path.
     * 
     * return path through tree
     * @param $id - ID
     * @return path
     */
    public function getPath($id) {
        $result = $this->find((int)$id);
        
        $this->_path[] = $result;
        
        if($result[0]->parent_id != 0) {
            $this->getPath($result[0]->parent_id);
        }
        
        return $this->_path;
    }
    
    public function resetTree() {
        $this->_tree = array();
    }
    
    /**
     * Complete select (Protected).
     * 
     * completes select request.
     * @param object $select
     * @param array $params
     */
    protected function _completeSelect(&$select, array $params) {
        if(isset($params['mandatory'])) {
            foreach($params['mandatory'] as $key => $column) {
                $select->where($column.'_'.$this->_locale.' != ""');
            }
        }
        
        if(isset($params['parent_type'])) {
            $select->where('parent_type = ?',$params['parent_type']);
        }
    }
}