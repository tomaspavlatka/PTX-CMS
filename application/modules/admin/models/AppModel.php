<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 7, 2010
**/

class Admin_Model_AppModel {
    
	/*
     * Id (Protected).
     * 
     * main id of the object
     */
    protected $_id;
    
    /*
     * DAO (Protected).
     * 
     * Database Adaptor Object
     */
    protected $_dao;
    
    /*
     * Data (Protected).
     * 
     * actual data of a record
     */
    protected $_data;
    
    /*
     * Languages (Protected).
     * 
     * list of active languages
     */
    protected $_languages;
    
    /**
     * Construct.
     * 
     * constructor for class
     */
    public function __construct() {
    	// Load active languages.
        $select = Admin_Model_DbSelect_Languages::pureSelect();
        $select->columns(array('id','code','name'))->where('status = 1');
        $stmt = $select->query();
        $this->_languages = $stmt->fetchAll();
    }
    
    /**
     * Bind Translation (Static).
     * 
     * binds translations.
     * @params array $data - data about record
     * @params array $transFields - fields with translation
     * @return array $add2Data - array with fields that must be added.
     */
    public static function bindTranslations(array $data, array $transFields) {
        $add2Array = array();
        $languages = Admin_Model_DbSelect_Languages::getActive();
        foreach($transFields as $fieldKey => $fieldName) {
            foreach($languages as $langKey => $langValues) {
                $columnName = $fieldName.'_'.$langValues['code']; // builds e.g. name_cs
                $ident = str_replace('_','',$columnName);
                
                $add2Array[$ident] = (isset($data[$columnName])) ? $data[$columnName] : null;
            }
        }

        return (array)$add2Array;
    }
    
    /**
     * Change position.
     * 
     * changes positions if possible
     * @param string $way - way
     * @param array $params - additional params.
     */
    public function changePosition($way, array $params = array()) {
        $this->getData(false,true);

        if(array_key_exists('parent_type',$this->_data)) {
            $params['parent_type'] = $this->_data['parent_type'];
        }
        
        $data4Change = $this->_dao->find4PositionChange($this->_data['parent_id'],$this->_data['position'],$way,$params);

        if(isset($data4Change->position)) {
            $this->_dao->updatePosition($this->_data['position'],$data4Change->id);
            $this->_dao->updatePosition($data4Change->position,$this->_id);
        }
    }   
    
    /**
     * Change status.
     * 
     * changes status for record - if it's possible
     * @return new value of status | null
     */
    public function changeStatus() {
        $this->getData(true);        
        
        if($this->_data->status == 0) {
            $this->_dao->updateStatus(1,$this->_id);
            return 1;    
        } else if($this->_data->status == 1) {
            $this->_dao->updateStatus(0,$this->_id);
            return 0;
        }
        
        return null;
    }
    
    /**
     * Delete.
     * 
     * deletes record from db.
     * @return number of affected rows in db
     */
    public function delete() {
        if($this->canBeDeleted()) {
           return $this->_dao->updateStatus(-1,$this->_id);
        } else {
            return -1;
        }   
    }
    
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
                $this->_data = $this->_data->toArray();
            	return $this->_data;
            } else {
                return array();
            }
        }
    }
    
    /**
     * Update.
     * 
     * updates data in database
     * @param array $formData - data to be updated
     * @param array $params - additional params
     * @return number of affected rows in db
     */
    public function update(array $formData, array $params = array()) {
        return $this->_dao->ptxUpdate($formData,$this->_id,$params);
    }
    
    /**
     * Update Featured Picture.
     * 
     * updated featured picture..
     * @param array $formData - data from form
     * @return number of affected rows in db
     */
    public function updateFeaturedPicture(array $formData) {
        if(isset($formData['path']) && !empty($formData['path'])) {
            if(file_exists('.'.$formData['path'])) {
                $imageSize = getimagesize('.'.$formData['path']);
                $saveData = array(
                    'imagefile'   => $formData['path'],
                    'imagewidth'  => $imageSize[0],
                    'imageheight' => $imageSize[1]);
                
                return $this->_dao->updateFeaturedPicture($saveData,$this->_id);
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
