<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 20, 2010
**/

class Admin_Model_Photos extends Admin_Model_AppModels {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        $this->_dao = new Admin_Model_DbTable_Photo();
    }
    
    /**
     * Import Picasa.
     * 
     * imports data from picasa     
     * @param array $photoData - data about photo
     * @param integer $categoryId - id of parent category
     * @param integer $position - position of photo
     * @param string $albumTitle - title of album
     * @return id of inserted record.
     */
    public function importPicasa(array $photoData, $categoryId, $position, $albumTitle) {
    	$dbData = $this->_dao->findByPhotoId($photoData['photoid']);
        
        if(empty($dbData)) {
            return $this->_dao->importPicasa($photoData,$categoryId,$position, $albumTitle);
        } else {
            $this->_dao->updatePicasa($photoData,$categoryId,$position,$dbData->id, $albumTitle);
            return (int)$dbData->id;
        }
    }
    
    /**
     * Update Position.
     * 
     * updates positions accordint to third params
     * @param string $parentType - type of parent
     * @param integer $parentId - id of parent
     * @param array $positions - new position order
     */
    public function updatePositions($parentType, $parentId, $positions) {
        foreach($positions as $key => $value) {
            $photoObj = new Admin_Model_Photo($value);
            $photoObj->updatePosition(($key+1));
        }
    }
}
