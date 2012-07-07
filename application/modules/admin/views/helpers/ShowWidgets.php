<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 10.8.2010
**/

class Admin_View_Helper_ShowWidgets {

    // Variables.
    private $_user;
    private $_widgets = array();
    private $_locale;
    
    /**
     * Constructor.
     * 
     * constructor of class
     */
    public function __construct() {
        $this->_user = Zend_Auth::getInstance()->getStorage()->read();
        $this->_locale = Zend_Registry::get('PTX_Locale');
    }
    
    /**
     * Show widget nazev role
     * @param $ident - identificator
     * @return $role     
     */
    public function showWidgets($place) {
        switch($place) {
            case 'left':
                return $this->_leftColumn();
        }
    }

    /*************** PRIVATE FUNCTION ****************/
    /**
     * nastavi widgety pro dane umisteni
     * @param $place - place
     */
    private function _getWidgets($place) {
        $select = Admin_Model_DbSelect_Widgets::pureSelect();
        $select->columns(array('name_'.$this->_locale.' as name','content_'.$this->_locale.' as content','parent_type'))
            ->where('name_'.$this->_locale.' != ""')->where('widget_place_id = ?',(int)$place)
            ->where('user_id = ?',(int)$this->_user->id)->where('status = 1')->order('position asc');
        $stmt = $select->query();
        $this->_widgets = $stmt->fetchAll();
    }
    
    /**
     * vrati levy sloupec     
     */
    private function _leftColumn() {
        $this->_getWidgets(1);

        $widgets = array(); $i = 0;
        foreach($this->_widgets as $row) {
            if($row['parent_type'] != 'separator') {
                $widgets[$i]['values'][] = $row;
            } else {
                $i++;
                $widgets[$i] = array('name' => $row['name']);
            }
        }
        
        $code = null;
        foreach($widgets as $widget) {
            $name = (isset($widget['name'])) ? $widget['name'] : 'Menu';
            $code .= '<ul class="box">';
            $code .= '<li class="submenu-active"><a href="#">'.$name.'</a>';
            if(isset($widget['values']) && !empty($widget['values'])) {
                $code .= '<ul>';
                foreach($widget['values'] as $key => $row) {
                    if($row['parent_type'] == 'shortcut') {
                        if(!empty($row['content'])) {
                            $code .= '<li><a href="'.$row['content'].'">'.$row['name'].'</a></li>';
                        }
                    }
                }
                $code .= '</ul>';
            }
            $code .= '</li></ul>';
        }
        
        return $code;
    }
}