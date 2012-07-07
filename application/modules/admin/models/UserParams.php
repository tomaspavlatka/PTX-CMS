<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 28.7.2010
**/

class Admin_Model_UserParams extends Admin_Model_AppModels {
    
    /************* PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        $this->_dao = new Admin_Model_DbTable_UserParam();
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
        return $this->_dao->getParamValues($idUser,$param);
    }
    
    /**
     * Param exists.
     * 
     * checks whether param exists in database
     * @param $param - param
     * @return true | false
     */
    public function paramExists($param,$idUser) {
        $data = $this->_dao->getParamValues($idUser,$param);
        
        return (isset($data[0]->id)) ? true : false;
    }
    
    /**
     * Save param.
     * 
     * saves new param into database
     * @param $idUser - ID of user
     * @param $param - parameter
     * @param $value - value
     * @param $notice - notice
     * @return id of inserted record into database
     */
    public function saveParam($idUser,$param,$value,$notice = null) {
        return $this->_dao->saveParam($idUser,$param,$value,$notice);
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
        return $this->_dao->updateParam($idUser,$param,$value,$notice);
    }
}