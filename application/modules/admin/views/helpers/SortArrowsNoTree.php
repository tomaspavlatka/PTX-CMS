<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 29.7.2010
**/

class Admin_View_Helper_SortArrowsNoTree {
    
    /************** VARIABLES **************/
    private $_db = null;
    private $_iconObject = null;
    private $_session;
    
    /**
     * constructor
     */
    public function __construct() {
        $this->_session = new Zend_Session_Namespace('admindata');        
        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }
    
    /************* PUBLIC FUNCTION **************/
    /**
     * vrati sipky
     * @param $idInput
     * @param $idParent
     * @param $table
     * @param $columnId
     * @param $columnParent
     * @param $columnPosition
     * @param $columnStatus
     * @param $script
     * @return sipky
     */
    public function sortArrowsNoTree($idInput,$table,$columnId,$columnPosition,$columnStatus,$script) {
        $current = $this->_getData($idInput,$table,$columnId);

        // priparvime si promenou
        $arrows = null;

        // nactem sipku dolu
        $arrows .= $this->_arrowDown($current[$columnPosition],$table,$idInput,$columnId,$columnPosition,$columnStatus,$script);

        // nactem oddelovac
        $arrows .= '&nbsp;';

        // nactem sipku nahoru
        $arrows .= $this->_arrowUp($current[$columnPosition],$table,$idInput,$columnId,$columnPosition,$columnStatus,$script);

        // vratime sipky
        return $arrows;
    }
    
    /************* PRIVATE FUNCTION **************/
    private function _arrowDown($position,$table,$idInput,$columnId,$columnPosition,$columnStatus,$script) {
        $select = new Zend_Db_Select($this->_db);
        $select->from($table)->where("{$columnPosition} > ? ",(int)$position)->where($columnStatus ." > -1")->order($columnPosition.' asc');
        $stmt = $select->query();
        $result = $stmt->fetch();
        
        if(isset($result[$columnId])) {
            echo '<a href="'.$script.'id-input/'.$idInput.'/way/down/" title="down">';
                echo '<img src="/images/icons/green/18/arrow_down.png" width="18" height="18" alt="down" />';
            echo '</a>';
        } else {
            echo '<img src="/images/icons/green/18/arrow_down_gray.png" width="18" height="18" alt="down" />';
        }
    }
    
    private function _arrowUp($position,$table,$idInput,$columnId,$columnPosition,$columnStatus,$script) {
        $select = new Zend_Db_Select($this->_db);
        $select->from($table)->where("{$columnPosition} < ? ",(int)$position)->where($columnStatus ." > -1")->order($columnPosition.' desc');
        
        $stmt = $select->query();
        $result = $stmt->fetch();
        
        if(isset($result[$columnId])) {
            echo '<a href="'.$script.'id-input/'.$idInput.'/way/up/" title="up">';             
                echo '<img src="/images/icons/green/18/arrow_up.png" width="18" height="18" alt="up" />';
            echo '</a>';               
        } else {
            echo '<img src="/images/icons/green/18/arrow_up_gray.png" width="18" height="18" alt="up" />';
        }
    }
    
    private function _getData($idInput,$table,$columnId) {
        $select = new Zend_Db_Select($this->_db);
        $select->from($table)->where("{$columnId} = ? ",(int)$idInput);
        
        $stmt = $select->query();
        return $stmt->fetch();
    }
}