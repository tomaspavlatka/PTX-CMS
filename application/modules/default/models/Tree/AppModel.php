<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 5, 2010
**/

class Default_Model_Tree_AppModel extends Zend_Db_Table {
    
    /************* VARIALBES *************/
    protected $_tree = array();
    protected $_path = array();
    protected $_name;
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Constructor
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct(); 
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
        if(isset($params['parent_type'])) {
            $select->where('parent_type = ?',$params['parent_type']);
        }        
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

        if($result instanceof Zend_Db_Table_Row) {
        	$data = $result->toArray();
        }
        
        if(isset($data['parent_id']) && $data['parent_id'] == 0) {
            return (array)$data;             
        } else {
            return $this->getTopParent($data['parent_id']);
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
        $select = $this->select()->where('id = ? ',(int)$id);        
        $result = $this->fetchRow($select);

        if($result instanceof Zend_Db_Table_Row) {
            $data = $result->toArray();
        }
        
        if(isset($data)) {
	        $this->_path[] = $data;
	        
	        if($data['parent_id'] != 0) {
	            $this->getPath($data['parent_id']);
	        }
	        
	        return $this->_path;
        }
    }
}