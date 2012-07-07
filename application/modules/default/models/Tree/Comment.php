<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 9.11.2010
**/

class Default_Model_Tree_Comment extends Default_Model_Tree_AppModel {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class.
     */
    public function __construct() {
        parent::__construct();

        $this->_name = 'comments';
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
        $select = $this->select()
            ->where('comment_id = ?',(int)$parentId)
            ->where('parent_id = ?',(int)$params['parent_id'])->where('parent_type = ?',$params['parent_type'])
            ->where('status '.$status)->order('id ASC');        
        $result = $this->fetchAll($select);
        
        foreach($result as $row){
            $array = $row->toArray();
            $array['level'] = $level;
            
            $this->_tree[] = $array;
            $this->getTree($row->id,$level+1,$status,$params);
        }
        
        return $this->_tree;
    }
}