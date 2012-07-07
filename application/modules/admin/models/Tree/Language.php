<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Oct 13, 2011
 */
  
class Admin_Model_Tree_Language extends Admin_Model_Tree_AppModel {
    
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct(); 
        
        $this->_name = 'languages';
        $this->_transFields = array('name');
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
        $select = $this->select()->where('status '.$status)->order('position ASC');   
        $this->_completeSelect($select,$params);
        $result = $this->fetchAll($select);
        
        foreach($result as $row){
            $array = $row->toArray();
            if(!empty($this->_transFields)) {
                foreach($this->_transFields as $key => $name) {
                    $array[$name] = $array[$name.'_'.$this->_locale];      
                }
            }
            $array['level'] = $level;
            
            $this->_tree[] = $array;
        }
        
        return $this->_tree;
    }
}