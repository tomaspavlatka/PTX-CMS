<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Sep 23, 2011
 */
  
class Admin_Model_DbTable_Banner extends Admin_Model_DbTable_AppModel {
    
    /************* PUBLIC FUNCTION ***************/
    /**
     * Constructor.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        $this->_name = 'banners';
        $this->_transFields = array('name','title','link');
    }

    /**
     * Update.
     * 
     * update data in database
     * @param $formData - data to be updated
     * @param $id - ID
     * @param $params - additional params
     * @return number of affected rows in db
     */
    public function ptxUpdate(array $formData, $id, array $params = array()) {
        $data = array(
            'parent_id'           => (int)$formData['parent'],
            'target'              => $formData['target'],
            'code'                => $formData['code'],
            'updated'             => time(),
            'status'              => $formData['status']);
        
        if($params['position_update']) {
        	$data['position'] = $this->_findPosition($formData['parent'], $params);
        }
        
        // Bind translations.
        $this->_bindTransFields($formData,$data);
        
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
     * @param $params - additional params   
     * @return id of inserted record
     */
    public function save(array $formData, array $params = array()) {
        $data = array(
            'parent_type'         => $params['parent_type'],
            'parent_id'           => (int)$formData['parent'],
            'target'              => $formData['target'],
            'image_file'          => $params['image_file'],
            'image_width'         => $params['image_width'],
            'image_height'        => $params['image_height'],
            'code'                => $formData['code'],
            'position'            => $this->_findPosition($formData['parent'], $params),
            'created'             => time(),
            'updated'             => time(),
            'status'              => $formData['status']);
        
        // Bind translations.
        $this->_bindTransFields($formData,$data);
        
        $id = parent::insert($data);
        if(is_numeric($id)) {
            $this->_saveLog($data);
        }
        return (int)$id; 
    }
    
    /**
     * Update logo.
     * 
     * update logo for category
     * @param string $filename - filename with logo
     * @param integer $id - ID of category
     * @param array $imageSize - information about image
     * @return number of affected rows in db
     */
    public function updateLogo($filename, $id, array $imageSize) {
        $data = array(
            'image_file'   => $filename,
            'image_width'  => $imageSize[0],
            'image_height' => $imageSize[1],
            'updated'      => time());
        
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
     * @param integer $parentId - id of the parent
     * @param array $params - additional params.
     * @return integer free position
     */
    protected function _findPosition($idParent, array $params = array()) {
        $select = $this->select()->where('parent_id = ? ',(int)$idParent)->where('status > -1')->order('position DESC');
        $select->where('parent_type = ?',$params['parent_type']);
        $data = $this->fetchRow($select);
        
        if(!isset($data->position)) {
            return 1;
        } else {
          return ($data->position+1);
        }
    }
}