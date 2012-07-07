<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Oct 22, 2011
 */
 
class Admin_Model_Location extends Admin_Model_AppModel {
    
    /**
     * Constructor
     * 
     * constructor of class
     * @param $id - ID of category
     */
    public function __construct($id) {
        $this->_dao = new Admin_Model_DbTable_Location();
        $this->_id = (int)$id;
    }
    
    /**
     * Can be deleted.
     * 
     * check whether category can be deleted
     * @return true | false
     */
    public function canBeDeleted() {
        
        // Has children.
        if($this->hasChildren()) {
            return false;
        }
        
        return true;
    }

    /**
     * Has children.
     * 
     * find out whether category has some children
     * @return true | false
     */
    public function hasChildren() {
        $select = Admin_Model_DbSelect_Locations::pureSelect();
        $select
            ->columns(array('COUNT(DISTINCT(id)) as count'))
            ->where('parent_id = ?',$this->_id)
            ->where('status > -1');
        $stmt = $select->query();
        $data = $stmt->fetchAll();
        
        return ($data[0]['count'] > 0);
    }
    
    /**
     * Update.
     * 
     * update data in db
     * @param $formData - data from form
     * @return number of affected rows in db
     */
    public function update(array $formData) {
        $this->_checkData(false,true);
        
        // Status was changed to inactive. 
        if($formData['status'] == 0 && $this->_data['status'] != 0) {
            // Deactivate also all children
            $this->_deactivateChildren();
        }

        $languages = Admin_Model_DbSelect_Languages::getActive(); 
        foreach($languages as $key => $values) {
            if($formData['name'.$values['code']] != $this->_data['name_'.$values['code']]) {
                $backupsObj = new Admin_Model_UrlBackups();
                $backupsObj->save($this->_id,'categories',$this->_data['url_hash_'.$values['code']],$values['code']);
            }
        }
        
        // Parent has been changed.
        $updPosition = ($this->_data['parent_id'] != $formData['parent']) ? true : false;
        
        // Make update.
        return $this->_dao->ptxUpdate($formData,$this->_id,$updPosition);
    }
    
    /**
     * Deactivate children.
     * 
     * deactivate all children for category as a parent
     */
    private function _deactivateChildren() {
        $treeObj = new Admin_Model_Tree_Category();
        $treeData = $treeObj->getTree($this->_id,0," = 1");

        foreach($treeData as $row) {
            $catObj = new Admin_Model_Category($row['id']);
            $catObj->changeStatus();
        }
    }
}