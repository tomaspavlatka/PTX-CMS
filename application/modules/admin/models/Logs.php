<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Oct 12, 2011
 */
  
class Admin_Model_Logs extends Admin_Model_AppModels {
    
    /**
     * Construct.
     * 
     * constructor of class
     */
    public function __construct() {
        $this->_dao = new Admin_Model_DbTable_Log();
    }
    
    /**
     * Archive.
     * 
     * archives logs order than timelimit
     * @param integer $timeLimit - time limit
     * @return array archive information
     */
    public function archive($timeLimit) {
    	$returnInfo = array();
    	
    	$data = $this->_dao->getOlderThen($timeLimit,true);
    	$returnInfo['count'] = count($data);
    	    	
    	// Save into file.
    	$fileName = date('Ymd');
    	$fOpen = fopen(APPLICATION_PATH.'/_tmp/_logs/'.$fileName,'w+');
    	fwrite($fOpen,serialize($data));
    	fclose($fOpen);
    	
    	// Clear table.
    	$idsArray = PTX_Page::getIdsArray($data, 'id');
    	$this->_dao->deleteIds($idsArray);
    	
    	return (array)$returnInfo;
    }
}