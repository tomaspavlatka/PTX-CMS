<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 23.8.2010
**/

class Admin_Model_Article extends Admin_Model_AppModel {
    
    /**
     * Construct.
     * 
     * constructor of class
     * @param $id - ID
     */
    public function __construct($id) {
        $this->_dao = new Admin_Model_DbTable_Article();
        $this->_id = (int)$id;
    }
 
    /**
     * Activate revision version
     * 
     * @param $revisionData - data from revision
     * @return number of affected rows in db
     */
    public function activateRevision($revisionData) {
        // Data to be saved.
        $data = array(
            'pcontent' => $revisionData->content,
            'pperex'   => $revisionData->perex);
        
        return $this->updateContent($data);
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
     * Save revision version
     * 
     * @param $formData - data about page
     * @return id of inserted record in db
     */
    public function saveRevision($formData) {
        // ID of logged user
        $idUser = Zend_Auth::getInstance()->getIdentity()->id;
        $languages = Admin_Model_DbSelect_Languages::getActive();   

        // Data to be saved.
        foreach($languages as $langKey => $langValues) {
            $data = array(
                'perex'   => $formData['perex'.$langValues['code']],
                'content' => $formData['pcontent'.$langValues['code']],
                'locale'  => $langValues['code'],
                'notice'  => $formData['notice'.$langValues['code']]);
            
            $revisionsObj = new Admin_Model_ContentHistories();
            $revisionsObj->save($data,$this->_id,$idUser,'articles');
        }
    }
}