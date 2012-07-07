<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Oct 26, 2011
 */
 
class Admin_Model_DbTable_CategoryRelation extends Admin_Model_DbTable_AppModel {
    
    /************* PUBLIC FUNCTION ***************/
    /**
     * Constructor.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        $this->_name = 'category_relations';
        $this->_transFields = array();
    }
    
    
    public function findRecord(array $params, $status = " > -1",$inArray = false) {
        $select = $this->select()->where('category_id = ?',(int)$params['category_id'])->where('parent_type = ?',$params['parent_type'])
            ->where('parent_id = ?',(int)$params['parent_id'])->where('status '.$status);
        $data = $this->fetchRow($select);
        
        if($inArray) {
            if($data instanceof Zend_Db_Table_Row) {
                return $data->toArray();
            } else {
                return array();
            }
        } else {
            return $data;
        }
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
            'category_id'         => (int)$formData['category_id'],
            'parent_type'         => $formData['parent_type'],
            'parent_id'           => $formData['parent_id'],
            'created'             => time(),
            'updated'             => time(),
            'status'              => 1);
        
        $id = parent::insert($data);
        if(is_numeric($id)) {
            $this->_saveLog($data);
        }
        return (int)$id; 
    }
}