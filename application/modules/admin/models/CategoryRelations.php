<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Oct 26, 2011
 */
 
class Admin_Model_CategoryRelations extends Admin_Model_AppModels {
    
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        $this->_dao = new Admin_Model_DbTable_CategoryRelation();
    }
    
    /**
     * Save.
     * 
     * saves data into database     
     * @param array $formData - data to be saved
     * @param array $params - additional params
     * @return id of inserted record 
     */
    public function save(array $formData, array $params = array()) {
        $data = $this->_dao->findRecord($formData,' > -1',true);
        
        if(empty($data)) {
            return $this->_dao->save($formData,$params);
        }
    }
}