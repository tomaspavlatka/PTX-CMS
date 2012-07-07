<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 9.11.2010
**/

class Admin_Model_Tree_Comment extends Admin_Model_Tree_AppModel {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct(); 
        
        $this->_name = 'comments';
    }
    
    /**
     * Get tree.
     * 
     * return tree view of comments
     * @param $idParent - ID of parent
     * @param $level - level
     * @param $status - status
     * @param $type - type of content
     * @param $idContent - id of content
     * @return strom
     */
    public function getTree($idParent,$level,$status,$type = null, $idContent = null) {
        $select = $this->select()->where('comment_id = ? ',(int)$idParent)->where('status '.$status)->order('id ASC');

        // Restrict to parent type.
        if(!empty($type)) {
            $select->where('parent_type = ?',$type);
        }
        
        // Restrict to content id.
        if(!empty($idContent)) {
            $select->where('parent_id = ?',$idContent);
        }
        
        $result = $this->fetchAll($select);

        foreach($result as $row){
            $array = $row->toArray();
            $array['level'] = $level;
            
            $this->_tree[] = $array;
            $this->getTree($row->id,$level+1,$status,$type,$idContent);
        }
        
        return $this->_tree;
    }
}