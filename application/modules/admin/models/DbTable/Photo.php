<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 20, 2010
**/

class Admin_Model_DbTable_Photo extends Admin_Model_DbTable_AppModel {
    
    /************* PUBLIC FUNCTION ***************/
    /**
     * Constructor.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        $this->_name = 'photos';
        $this->_transFields = array('name','perex','seo_keywords','seo_description','url','url_hash');
    }
    
    /** 
     * Find for position change.
     * 
     * find record in db suitable for change position
     * @param $idParent - ID of parent
     * @param $position - position
     * @param $way - way
     * @param $params - additional params
     * @return data from db      
     */
    public function find4PositionChange($idParent,$position,$way,array $params = array()) {
        $select = $this->select();
        $select->where('parent_id = ? ',(int)$idParent)->where('status > -1')->where('parent_type = ?',$params['parent_type']);
        
        
        switch($way) {
            case 'down' :
                $select->where('position > ? ',(int)$position)->order('position asc');
                break;    
            case 'up' :
                $select->where('position < ? ',(int)$position)->order('position desc');
                break;
        }
        
        return $this->fetchRow($select);
    }
    
    /**
     * Find by photo id.
     * 
     * finds record according to photo id (stored in picasa_id column).
     * @param string $photoId - photo id
     * @return data
     */
    public function findByPhotoId($photoId) {
        $select = $this->select();
        $select->where('picasa_id = ?',$photoId)->where('status > -1');
        return $this->fetchRow($select);
    }
    
    /**
     * Import picasa.
     * 
     * imports data from picasa web album
     * @param array $photoData - data about photo
     * @param integer $categoryId - id of the parent category
     * @param integer $position - position (MAKE +1!)
     * @return id of inserted record.
     */
    public function importPicasa(array $photoData, $categoryId, $position) {
        $url = PTX_Uri::getUri($photoData['title']);
        $imageSize = getimagesize($photoData['src']);
        $data = array(
            'parent_id'           => (int)$categoryId,
            'parent_type'         => 'photos',
            'name'                => $photoData['title'],
            'url'                 => $url,
            'url_hash'            => md5($url),
            'file_name'           => $photoData['src'],
            'position'            => ($position+1),
            'picasa_id'           => $photoData['photoid'],
            'published'           => strtotime($photoData['published']),   
            'image_width'         => $imageSize[0], 
            'image_height'         => $imageSize[1], 
            'created'             => time(),
            'updated'             => time(),
            'status'              => 1);
        
        $id = parent::insert($data);
        if(is_numeric($id)) {
            $this->_saveLog($data);
        }
        return (int)$id; 
    }
    
    /**
     * Update.
     * 
     * update data in database
     * @param $formData - data to be updated
     * @param $id - ID
     * @param $updPosition - position should be updated 
     * @param $parentType - type of parent
     * @return number of affected rows in db
     */
    public function ptxUpdate(array $formData, $id, $updPosition,$parentType) {
        $data = array(
            'updated'  => time(),
            'status'   => $formData['status']);
        
        if($updPosition) {
            $data['position'] = $this->_findPosition($formData['parent'],$parentType);
        }
        
        // Bind translations.
        $this->_bindTransFields($formData,$data,array('url','url_hash'));
        $this->_bindUrls($data);
        
        $where = $this->getAdapter()->quoteInto('id = ?',(int)$id);
        $rows = parent::update($data,$where); 
        if($rows > 0) {
            $this->_saveLog($data,$where);
        }
        return (int)$rows;
    }
    
    /**
     * Save.
     * 
     * save new record into database.
     * @param $formData - data to be saved.     
     * @param $filename - filename with picture 
     * @param $parentType - parent type for category 
     * @return id of inserted record
     */
    public function save(array $formData,$params) {
        $data = array(
            'parent_id'           => (int)$formData['parent'],
            'parent_type'         => $params['parent_type'],
            'file_name'           => $params['image_file'],
            'image_width'         => $params['image_width'],
            'image_height'        => $params['image_height'],
            'position'            => $this->_findPosition($formData['parent'],$params['parent_type']),
            'note'                => (isset($formData['note'])) ? $formData['note'] : null,
            'published'           => time(),
            'created'             => time(),
            'updated'             => time(),
            'status'              => $formData['status']);
        
        // Bind translations.
        $this->_bindTransFields($formData,$data,array('url','url_hash'));
        $this->_bindUrls($data);
        
        $id = parent::insert($data);
        if(is_numeric($id)) {
            $this->_saveLog($data);
        }
        return (int)$id; 
    }   
    
    /**
     * Update picasa.
     * 
     * updates data from picasa web album
     * @param array $photoData - data about album
     * @param integer $categoryId - id of parent category
     * @param integer $position - position (MAKE +1!)
     * @param integer $id - id of record
     * @return number o affected rows in db.
     */
    public function updatePicasa(array $photoData, $categoryId, $position, $id) {
        $url = PTX_Uri::getUri($photoData['title']);
        $data = array(
            'parent_id'           => (int)$categoryId,
            'name'                => $photoData['title'],
            'perex'                => $photoData['summary'],
            'url'                 => $url,
            'url_hash'            => md5($url),
            'position'            => ($position+1),
            'updated'             => time());
        
        $where = $this->getAdapter()->quoteInto('id = ?',(int)$id);
        $rows = parent::update($data,$where); 
        if($rows > 0) {
            $this->_saveLog($data,$where);
        }
        return (int)$rows;
    }
    
    /**
     * Find position.
     * 
     * find free position for save
     * @param $idParent - ID parent
     * @param $parentType - parent type for category
     * @return free position
     */
    protected function _findPosition($idParent,$parentType) {
        $select = $this->select()->where('parent_id = ? ',(int)$idParent)->where('status > -1')->order('position DESC')->where('parent_type = ?',$parentType);
        $data = $this->fetchRow($select);
        
        if(!isset($data->position)) {
            return 1;
        } else {
          return ($data->position+1);
        }
    }
}