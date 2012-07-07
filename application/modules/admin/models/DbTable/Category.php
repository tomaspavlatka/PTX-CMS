<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 20.11.2010
**/

class Admin_Model_DbTable_Category extends Admin_Model_DbTable_AppModel {
    
    /************* PUBLIC FUNCTION ***************/
    /**
     * Constructor.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        $this->_name = 'categories';
        $this->_transFields = array('name','seo_keywords','perex','seo_description','url','url_hash');
    }
    
    /** 
     * Find for position change.
     * 
     * find record in db suitable for change position
     * @param $idParent - ID of parent
     * @param $position - position
     * @param $way - way
     * @param $parentType - parent type for category
     * @return data from db      
     */
    public function find4PositionChange($idParent,$position,$way,$parentType) {
        $select = $this->select();
        $select->where('parent_id = ? ',(int)$idParent)->where('status > -1')->where('parent_type = ?',$parentType);
        
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
     * Find by Album id.
     * 
     * finds record according to album id (stored in picasa_id column).
     * @param string $albumId - album id
     * @return data
     */
    public function findByAlbumId($albumId) {
        $select = $this->select();
        $select->where('picasa_id = ?',$albumId)->where('status > -1');
        return $this->fetchRow($select);
    }
    
    /**
     * Import picasa.
     * 
     * imports data from picasa web album
     * @param array $albumData - data about album
     * @return id of inserted record.
     */
    public function importPicasa(array $albumData) {
    	$url = PTX_Uri::getUri($albumData['title']);
        $data = array(
            'parent_id'           => 0,
            'parent_type'         => 'photos',
            'name'                => $albumData['title'],
            'url'                 => $url,
            'url_hash'            => md5($url),
            'logo'                => $albumData['photos']['photos'][0]['src'],
            'perex'               => $albumData['summary'],
            'picasa_id'           => $albumData['albumid'],
            'position'            => $this->_findPosition(0,'photos'),
            'published'           => strtotime($albumData['published']),
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
     * @param $parentType - parent type for category
     * @return number of affected rows in db
     */
    public function ptxUpdate(array $formData, $id,$updPosition,$parentType) {
        $data = array(
            'parent_id' => (int)$formData['parent'],
            'updated'   => time(),
            'status'    => (int)$formData['status']);
            
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
     * @param array $formData - data to be saved.     
     * @param array $params - additional params 
     * @return id of inserted record
     */
    public function save(array $formData,array $params = array()) {
        $data = array(
            'parent_id'           => (int)$formData['parent'],
            'parent_type'         => $params['parent_type'],
            'picasa_id'           => (isset($formData['picasa_id'])) ? $formData['picasa_id'] : null,
            'position'            => $this->_findPosition($formData['parent'],$params['parent_type']),
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
     * Update last input.
     * 
     * update timestamp about last input for specific category
     * @param $id - ID of category
     * @return number of affected rows in db
     */
    public function updateLastInput($id) {
        $data = array('last_input' => time());
        
        $where = $this->getAdapter()->quoteInto('id = ?',(int)$id);
        $rows = parent::update($data,$where); 
        if($rows > 0) {
            $this->_saveLog($data,$where);
        }
        return (int)$rows;
    }    
    
    /**
     * Update picasa.
     * 
     * updates data from picasa web album
     * @param array $albumData - data about album
     * @param integer $id - id of record
     * @return number o affected rows in db.
     */
    public function updatePicasa(array $albumData, $id) {
        $url = PTX_Uri::getUri($albumData['title']);
        $data = array(
            'name'                => $albumData['title'],
            'url'                 => $url,
            'url_hash'            => md5($url),
            'logo'                => $albumData['photos']['photos'][0]['src'],
            'perex'               => $albumData['summary'],
            'published'           => strtotime($albumData['published']),
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