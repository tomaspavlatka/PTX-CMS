<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Nov 9, 2011
 */
 
class Advert_Model_DbTable_AppModel extends Zend_Db_Table {
    
    protected $_name;
    protected $_locale;
    
    /**
     * Constructor.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        $this->_locale = Zend_Registry::get('PTX_Locale');
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
     * Update status.
     * 
     * updates status of record
     * @param $value - new status
     * @param $id - ID
     * @return number of affected records in db
     */
    public function updateStatus($value,$id) {
        $data = array('status' => $value, 'updated'=>time());
        
        $where = $this->getAdapter()->quoteInto('id = ?',(int)$id);     
        $rows = parent::update($data,$where); 
        if($rows > 0) {
            $this->_saveLog($data,$where);
        }
        return (int)$rows;
    }
    
    /**
     * Save log.
     * 
     * saves log about operation
     */
    protected function _saveLog($data,$where = null){
        $idUser = 999999;
        
        $content = null;
        foreach($data as $key => $value) {
            $content .= '['.$key.': '.$value.'] ';
        }
        
        $logsObj = new Default_Model_Logs();
        $logsObj->save($idUser,$this->_name,$where,$content);
    }
}