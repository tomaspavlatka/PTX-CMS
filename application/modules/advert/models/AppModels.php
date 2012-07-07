<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Nov 9, 2011
 */
 
class Advert_Model_AppModels {
    protected $_dao;
    
    /**
     * Contstruct.
     * 
     * constructor of class
     */
    public function __construct() {}
    
    /**
     * Save.
     * 
     * saves data into database     
     * @param array $formData - data to be saved
     * @param array $params - additional params
     * @return id of inserted record 
     */
    public function save(array $formData, array $params = array()) {
        return $this->_dao->save($formData, $params);
    }
}