<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 7.11.2010
**/

class Admin_Model_Relatives extends Admin_Model_AppModels {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        $this->_dao = new Admin_Model_DbTable_Relative();
    }

    /**
     * Delete.
     * 
     * deletes relation between parent and relative
     * @param $idParent - id of parent
     * @param $idRelative - id of relative      
     * @return number of affected rows in db
     */
    public function delete($idParent,$idRelative) {
        return $this->_dao->ptxDelete($idParent,$idRelative);
    }
    
    /**
     * Exits.
     * 
     * checks wether relation already exists
     * @param $idParent - id of parent 
     * @param $idRelative - id of relative
     * @return true | false
     */
    public function exists($idParent,$idRelative) {
        $data = $this->_dao->getRelation($idParent,$idRelative);
        
        return (isset($data->id)) ? true : false;
    }
    
    /**
     * Get 4 parent.
     * 
     * returns relatives for parent
     * @param $idParent - id article
     * @param $parentType - type of parent
     * @param $status - status of relations
     * @param $inArray - return result in array
     * @return relative articles
     */
    public function get4Parent($idParent, $parentType, $status = " > -1", $inArray = false) {
        $data = $this->_dao->get4Parent($idParent,$parentType,$status);
        
        if(!$inArray) {
            return $data;
        } else if($data instanceof Zend_Db_Table_Rowset) {
            return $data->toArray();
        } else {
            return array();
        }
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
        return $this->_dao->save($idArticle,$idRelative,$type);
    }
}
