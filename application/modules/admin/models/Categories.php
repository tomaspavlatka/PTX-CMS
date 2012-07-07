<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 20.11.2010
**/

class Admin_Model_Categories extends Admin_Model_AppModels {
    
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        $this->_dao = new Admin_Model_DbTable_Category();
    }
    
    /**
     * Import picasa.
     * 
     * imports category from picasa.     
     * @param array $albumData - data about albums
     * @return integer id of category
     */
    public function importPicasa(array $albumData) {
        $dbData = $this->_dao->findByAlbumId($albumData['albumid']);
        
        if(empty($dbData)) {
        	return $this->_dao->importPicasa($albumData);
        } else {
        	$this->_dao->updatePicasa($albumData,$dbData->id);
        	return (int)$dbData->id;
        }
    }
}
