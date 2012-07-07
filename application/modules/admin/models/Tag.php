<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 18, 2010
**/

class Admin_Model_Tag extends Admin_Model_AppModel {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     * @param $id - ID
     */
    public function __construct($id) {
        $this->_dao = new Admin_Model_DbTable_Tag();
        $this->_id = (int)$id;
    }
    
    /**
     * Can be deleted.
     * 
     * checks whether record can be deleted.
     * @return true | false
     */
    public function canBeDeleted() {
        return true;
    }
    
    /**
     * Update.
     * 
     * updates data about record in db
     * @param $formData - data about record
     * @return number of affected rows in db
     */
    public function update(array $formData) {
        $this->_checkData(false,true);
        $languages = Admin_Model_DbSelect_Languages::getActive();   
            
        foreach($languages as $key => $values) {
            if($formData['name'.$values['code']] != $this->_data['name_'.$values['code']]) {
                $backupsObj = new Admin_Model_UrlBackups();
                $backupsObj->save($this->_id,'tags',$this->_data['url_hash_'.$values['code']],$values['code']);
            }
        }
        
        return $this->_dao->ptxUpdate($formData,$this->_id);
    }
}
