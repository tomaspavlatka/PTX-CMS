<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Sep 21, 2011
 */
  
class Admin_Model_Tree_Menu extends Admin_Model_Tree_AppModel {
    
    /**
     * Construct.
     * 
     * constructor of class 
     */
    public function __construct() {
        parent::__construct();
        $this->_name = 'menu_inputs';
        $this->_transFields = array('name','title','link'); 
    }
    
    /**
     * Get Tree
     * 
     * return tree according to parameters
     * @param integer $parentId - ID of parent
     * @param integer $level - level
     * @param string  $status - status
     * @param integer $placeId - id of a place for menu
     * @return tree
     */
    public function getTree($parentId,$level,$status,array $params = array()) {
        $select = $this->select()->where('parent_id = ? ',(int)$parentId)->where('status '.$status)->order('position ASC')->where('menu_place_id = ?',(int)$params['place_id']);
        $this->_completeSelect($select,$params);        
        $result = $this->fetchAll($select);
        
        foreach($result as $row){
            $array = $row->toArray();
            $array['level'] = $level;
            
            if(!empty($this->_transFields)) {
                foreach($this->_transFields as $key => $name) {
                    $array[$name] = $array[$name.'_'.$this->_locale];      
                }
            }
            
            $this->_tree[] = $array;
            $this->getTree($row->id,$level+1,$status,$params);
        }
        
        return $this->_tree;
    }
}