<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Sep 21, 2011
 */
  
class Default_Model_Tree_Menu extends Default_Model_Tree_AppModel {
    
    /**
     * Construct.
     * 
     * constructor of class 
     */
    public function __construct() {
        parent::__construct();
        $this->_name = 'menu_inputs'; 
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
    public function getTree($parentId,$level,$status,$placeId) {
        $select = $this->select()->where('parent_id = ? ',(int)$parentId)->where('status '.$status)->order('position ASC')->where('menu_place_id = ?',(int)$placeId);        
        echo $select->__toString();exit;
        $result = $this->fetchAll($select);
                                                      
        foreach($result as $row){
            $array = $row->toArray();
            $array['level'] = $level;
            
            $this->_tree[] = $array;
            $this->getTree($row->id,$level+1,$status,$placeId);
        }
        
        return $this->_tree;
    }
}