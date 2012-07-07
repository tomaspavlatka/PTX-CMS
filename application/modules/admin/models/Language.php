<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Oct 13, 2011
 */

class Admin_Model_Language extends Admin_Model_AppModel {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     * @param $id - ID
     */
    public function __construct($id) {
        $this->_dao = new Admin_Model_DbTable_Language();
        $this->_id = (int)$id;
    }    
    
    /**
     * Can be delete.
     * 
     * checks whether record can be deleted
     * @return true | false
     */
    public function canBeDeleted() {
        // TODO.
        return true;
    }
    
    /**
     * Delete.
     * 
     * deletes record from db.
     * @return number of affected rows in db
     */
    public function delete() {
        if($this->canBeDeleted()) {
            // Delete.
            $rows = $this->_dao->updateStatus(-1,$this->_id);
           
            $this->getData(false,true);
            
            // Build database.
            $config = Zend_Registry::get('config');
            $params = array(
                'db_host'       => $config->resources->db->params->host,
                'db_name'       => $config->resources->db->params->dbname,
                'db_user'       => $config->resources->db->params->username,
                'db_password'   => $config->resources->db->params->password,
                'db_charset'    => $config->resources->db->params->charset);
            $dbObj = new PTX_Database($params);
            $dbObj->connect();
            $dbObj->removeLanguage($this->_data['code']);
            
            // Return result.
            return (int)$rows;
        } else {
            return -1;
        }   
    }
}