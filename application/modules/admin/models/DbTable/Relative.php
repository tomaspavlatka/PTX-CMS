<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 7.11.2010
**/

class Admin_Model_DbTable_Relative extends Admin_Model_DbTable_AppModel {
    
    /************* PUBLIC FUNCTION ***************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        
        $this->_name = 'relatives';
    }
    
    /**
     * Get relation.
     * 
     * try to find record according to parent and relative ids
     * @param $idParent - id of parent 
     * @param $idRelative - id of relative
     * @return data from db (fetchRow)
     */
    public function getRelation($idParent,$idRelative) {
        $select = $this->select()->where('parent_id = ?',(int)$idArticle)->where('relative_id = ?',(int)$idRelative);
        return parent::fetchRow($select);
    }
    
    /**
     * Get 4 parent.
     * 
     * returns relatives for parent
     * @param $idParent - id article
     * @param $paent_type - type of parent
     * @param $status - status of relations
     * @return data from db (fetchAll)
     */
    public function get4Parent($idParent,$parentType,$status) {
        $select = $this->select()->where('parent_id = ?',(int)$idParent)->where('status '.$status)->where('parent_type = ?',$parentType);
        return parent::fetchAll($select);
    }
    
    /**
     * Delete.
     * 
     * deletes relation between parent and relative
     * @param $idParent - id of parent
     * @param $idRelative - id of relative      
     * @return number of affected rows in db
     */
    public function ptxDelete($idParent,$idRelative) {
        $where = 'parent_id = '.(int)$idParent .' AND relative_id = '.(int)$idRelative;
        $rows = parent::delete($where);
        
        if(is_numeric($id)) {
            $data = array('operation'=>'DELETE');
            $this->_saveLog($data,$where);
        }
        return (int)$rows;
    }
    
    /**
     * Save.
     * 
     * saves new relation between parent and relative
     * @param $idArticle - id of parent
     * @param $idRelative - id of relative
     * @param $type - parent type  
     * @return id of inserted record   
     */
    public function save($idArticle,$idRelative,$type) {
        $data = array(            
            'parent_id'   => (int)$idArticle,
            'parent_type' => $type,
            'relative_id' => (int)$idRelative);
        
        $id = parent::insert($data);
        if(is_numeric($id)) {
            $this->_saveLog($data);
        }
        return (int)$id; 
    }
}