<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 22.8.2010
**/

class Admin_Model_StaticPage extends Admin_Model_AppModel {
    
    /**
     * Construct.
     * 
     * constructor of class
     * @param $id - ID
     */
    public function __construct($id) {
        $this->_dao = new Admin_Model_DbTable_StaticPage();
        $this->_id = (int)$id;
    }
    
    /**
     * Activate revision version
     * 
     * @param $revisionData - data from revision
     */
    public function activateRevision($revisionData) {
        // Data to be saved.
        $data = array('pcontent' => $revisionData->content);
        $this->updateContent($data);
    }
    
    /**
     * Can be deleted.
     * 
     * checks whether record can be deleted
     * @return true | false
     */
    public function canBeDeleted() {
        return true;
    }
    
    
    /**
     * Save revision.
     * 
     * save new revision version
     * @param $formData - data about page
     * @return id of new inserted record in db
     */
    public function saveRevision(array $formData) {
        // ID of logged user
        $idUser = Zend_Auth::getInstance()->getIdentity()->id;
        $languages = Admin_Model_DbSelect_Languages::getActive();   

        // Data to be saved.
        foreach($languages as $langKey => $langValues) {
	        $data = array(
	            'perex'   => null,
	            'content' => $formData['pcontent'.$langValues['code']],
	            'locale'  => $langValues['code'],
	            'notice'  => $formData['notice'.$langValues['code']]);
	        
	        $revisionsObj = new Admin_Model_ContentHistories();
	        $revisionsObj->save($data,$this->_id,$idUser,'pages');
        }
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
                $backupsObj->save($this->_id,'pages',$this->_data['url_hash_'.$values['code']],$values['code']);
            }
        }
        
        return $this->_dao->ptxUpdate($formData,$this->_id);
    }
}