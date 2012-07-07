<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 28.7.2010
**/

class Admin_Model_DbTable_UserParam extends Admin_Model_DbTable_AppModel {
    
    /************* VARIABLES *************/
    protected $_name = 'user_param';
    
    /************* PUBLIC FUNCTION ***************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        parent::__construct();
        $this->_name = 'user_params';
    }
    
    /**
     * Get param values.
     * 
     * returns all values for specific param and usr
     * @param $idUser - ID of user
     * @param $param - ID of param
     * @return data from database
     */
    public function getParamValues($idUser,$param) {
        $select = $this->select()->where('user_id = ?',(int)$idUser)->where('param = ?',$param)->order('created desc');
        return parent::fetchAll($select);
    }
    
    /**
     * Save param.
     * 
     * saves new data into database
     * @param $idUser - ID of user
     * @param $param - parameter
     * @param $value - value
     * @param $notice - notice
     * @return id of inserted record
     */
    public function saveParam($idUser,$param,$value,$notice = null) {
        $data = array(
            'id_user'       => (int)$idUser,
            'param'         => $param,
            'value'         => $value,
            'notice'        => $notice,
            'created'       => time());
        
        $id = parent::insert($data);
        if(is_numeric($id)) {
            $this->_saveLog($data);
        }
        return (int)$id;
    }
    
    /**
     * Update param.
     * 
     * updates param's data in database
     * @param $idUser - ID of user
     * @param $param - parameter
     * @param $value - value
     * @param $notice - notice
     * @return number of affected rows in database
     */
    public function updateParam($idUser,$param,$value,$notice = null) {
        $data = array(
            'value'         => $value,
            'notice'        => $notice,
            'created'       => time());
        
        $where = "id_user = '{$idUser}' AND param = '{$param}'";
        $rows = parent::update($data,$where);
        if(is_numeric($id) && $rows > 0) {
            $this->_saveLog($data,$where);
        }
        return (int)$rows;
    }
}