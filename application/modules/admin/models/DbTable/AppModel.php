<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 6, 2010
**/

class Admin_Model_DbTable_AppModel extends Zend_Db_Table {

	/*
	 * Name (Protected).
	 * 
	 * holds name of table.
	 */
    protected $_name;
    
    /*
     * TransFields (Protected).
     * 
     * holds name of fields with translation.
     */
    protected $_transFields = array();
    
    /**
     * Constructor.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
    }
    

    /**
     * Get children.
     * 
     * return all children for parent
     * @param $idParent - ID parent
     * @param $status - status 
     * @return all children
     */
    public function getChildren($idParent,$status = " > -1") {
       $select = $this->select()->where('parent_id = ?',(int)$idParent)->where('status '.$status)->order('position ASC');
       return parent::fetchAll($select);
    }
    
    /**
     * Get row.
     * 
     * get rows from database
     * @param $id - ID
     * @return radek z db
     */
    public function getRow($id) {
        $select = $this->select()->where('id = ?',(int)$id);
        return parent::fetchRow($select);
    }
    
    /** 
     * Find for position change.
     * 
     * find record in db suitable for change position
     * @param $idParent - ID of parent
     * @param $position - position
     * @param $way - way
     * @param array $params - additional params.
     * @return data from db      
     */
    public function find4PositionChange($idParent,$position,$way, array $params = array()) {
        $select = $this->select();
        $select->where('parent_id = ? ',(int)$idParent)->where('status > -1');
        
        if(isset($params['parent_type']) && !empty($params['parent_type'])) {
        	$select->where('parent_type = ?',(string)$params['parent_type']);
        }
        
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
     * Update Featured Picture.
     * 
     * updated featured picture..
     * @param array $formData - data from form
     * @param integer $id - id of record in db
     * @return number of affected rows in db
     */
    public function updateFeaturedPicture(array $formData, $id) {
        $data = array(            
            'image_file'   => $formData['imagefile'],
            'image_width'  => $formData['imagewidth'],
            'image_height' => $formData['imageheight'],
            'updated'      => time());
        
        $where = $this->getAdapter()->quoteInto('id = ?',(int)$id);
        $rows = parent::update($data,$where); 
        if($rows > 0) {
            $this->_saveLog($data,$where);
        }
        return (int)$rows;
    }
    
    /**
     * Update position.
     * 
     * updates position of record
     * @param $value - new position
     * @param $id - ID
     * @return number of affected records in db
     */
    public function updatePosition($value,$id) {
        $data = array('position' => $value);
        
        $where = $this->getAdapter()->quoteInto('id = ?',(int)$id);     

        $rows = parent::update($data,$where); 
        if($rows > 0) {
            $this->_saveLog($data,$where);
        }
        return (int)$rows;
    }
    
    /**
     * Update status.
     * 
     * updates status of record
     * @param $value - new status
     * @param $id - ID
     * @return number of affected records in db
     */
    public function updateStatus($value,$id) {
        $data = array('status' => $value);
        
        $where = $this->getAdapter()->quoteInto('id = ?',(int)$id);     

        $rows = parent::update($data,$where); 
        if($rows > 0) {
            $this->_saveLog($data,$where);
        }
        return (int)$rows;
    }
    
    /**
     * Bind Image (Protected).
     * 
     * binds information about image
     * @param array $formData - data from form
     * @param array $data - data to be saved
     */
    protected function _bindImage(array $formData, &$data) {
        $data['image_file']   = null;
        $data['image_width']  = null;
        $data['image_height'] = null;
                
        if(isset($formData['image']) && !empty($formData['image'])) {
            if(@file_exists('.'.$formData['image'])) {
                $imagesize = @getimagesize('.'.$formData['image']);
                $data['image_file'] = $formData['image'];
                $data['image_width'] = $imagesize[0];
                $data['image_height'] = $imagesize[1];
            }
        } 
    }
    
    /**
     * Bind Trans Fields (Protected).
     * 
     * binds fields with translations.
     * @param array $formData - data from post
     * @param array $data - original dat
     * @param array $skip - array with fields from transFields that should be skipped
     * @return array fields that must be added.     
     */
    protected function _bindTransFields(array $formData, &$data, array $skip = array()) {
        $languages = Admin_Model_DbSelect_Languages::getActive();
    	foreach($this->_transFields as $fieldKey => $fieldName) {
        	if(!in_array($fieldName,$skip)) {
	    		foreach($languages as $langKey => $langValues) {
	        		$columnName = $fieldName.'_'.$langValues['code']; // builds e.g. name_cs
	        		$ident = str_replace('_','',$columnName);
	        		
	        		$data[$columnName] = (isset($formData[$ident])) ? $formData[$ident] : null;
	        	}
        	}
        }
    }
    
    /**
     * Bind Urls (Protected).
     * 
     * binds urls.
     * @param array $data - data
     */
    protected function _bindUrls(&$data) {
        $languges = Admin_Model_DbSelect_Languages::getActive();
        foreach($languges as $langKey => $langValues) {
            $data['url_'.$langValues['code']] = PTX_Uri::getUri($data['name_'.$langValues['code']]);
            $data['url_hash_'.$langValues['code']] = md5(PTX_Uri::getUri($data['url_'.$langValues['code']]));
        }
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
        $data = $this->fetchRow($select);
        
        if(!isset($data->position)) {
            return 1;
        } else {
          return ($data->position+1);
        }
    }
    
    /**
     * Save log.
     * 
     * saves log about operation
     */
    protected function _saveLog($data,$where = null){
        $idUser = Zend_Auth::getInstance()->getStorage()->read()->id;
        
        $content = null;
        foreach($data as $key => $value) {
            $content .= '['.$key.': '.$value.'] ';
        }
        
        $logsObj = new Default_Model_Logs();
        $logsObj->save($idUser,$this->_name,$where,$content);
    }
    
    /**
     * Transform date.
     * 
     * transforms date into mktime   
     * @param $date - date
     * @return mktime
     */
    protected function _transformDate($date) {
        $explode = explode(' ',$date);
        $timeExplode = explode(':',$explode[1]);
        $dateExplode = explode('/',$explode[0]);
        
        return mktime($timeExplode[0],$timeExplode[1],$timeExplode[2],$dateExplode[1],$dateExplode[0],$dateExplode[2]);
    }
}