<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Oct 13, 2011
 */
  
class Admin_Model_Languages extends Admin_Model_AppModels {
    
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        $this->_dao = new Admin_Model_DbTable_Language();
    }
    
    /**
     * Code Exists.
     * 
     * checks whether code exists in database
     * @param string $code - code
     * @param string $status - status
     * @param integer $exclude - id to be excluded
     * @retrun boolean true | false
     */
    public function codeExists($code, $status = ' > -1', $exclude = null) {
    	$data = $this->_dao->findByCode($code,$status,true);
    	
    	if(!empty($data) && $data['id'] != $exclude) {
    		return true;
    	} else {
    		return false;
    	}
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
        // Save.
        $languageId = $this->_dao->save($formData, $params);
        
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
        $dbObj->addLanguage($formData['code']);
        
        // Return id.
        return (int)$languageId;
    }
}