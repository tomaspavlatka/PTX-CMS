<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Nov 9, 2011
 */
 
class Advert_Model_AppModel {
    
    protected $_id;
    protected $_dao;
    protected $_data;
    
    /**
     * Construct.
     * 
     * constructor for class
     */
    public function __construct() {}
    
    /**
     * Get data.
     * 
     * returns data for specific row in db
     * @param $reload - force reload
     * @param $inArray - data in array ?
     * @return data object
     */
    public function getData($reload = false,$inArray = false) {
        $this->_checkData($reload);
        
        if(!$inArray) {
            return $this->_data;
        } else {
            if($this->_data instanceof Zend_Db_Table_Row_Abstract) {
                return $this->_data->toArray();
            } else {
                return array();
            }
        }
    }
    
    /**
     * Check data.
     * 
     * checks whether we have data from db
     * @param $reload - force reload
     */
    protected function _checkData($reload) {
        if($reload || !($this->_data instanceof Zend_Db_Table_Row_Abstract)) {
            $this->_data = $this->_dao->getRow($this->_id);
        }
    }
}