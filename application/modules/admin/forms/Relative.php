<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 7.11.2010
**/

class Admin_Form_Relative extends Admin_Form_AppForm {
    
   
    /*********** PUBLIC FUNCTION *************/
    /**
     * Construct.
     * 
     * constructor of class
     * @param $options - option     
     */
    public function __construct($options = null) {
        parent::__construct($options);
        
        // Translations.
        $this->_loadTranslation();

        // Relative.
        $relative = new Zend_Form_Element_Select('relative');
        $relative
            ->setLabel($this->_trans->trans('Relative'))
            ->setOptions(array('class'=>"long"))
            ->addMultiOption(null,$this->_trans->trans('- select relative -'))
            ->setRequired(true);        
        
        // nacte strom            
        $relatives = $this->_getRelatives($options);       
        foreach($relatives as $row) {
            $date = new Zend_Date($row['published']);
            $name = $row['name'] .' ['.$date->get(Zend_Date::DATETIME_SHORT).']';
            $relative->addMultiOption($row['id'],$name);
        }
                    
        // reverse //
        $reverse = new Zend_Form_Element_Radio('reverse');
        $reverse
            ->setLabel($this->_trans->trans('Reverse relation'))
            ->addMultiOption(1,$this->_trans->trans('Yes'))
            ->addMultiOption(0,$this->_trans->trans('No'))
            ->setValue(0);   
            
        // submit //
        $submit = new Zend_Form_Element_Submit('submit');
        $submit
            ->setOptions(array('class'=>"input-submit"))    
            ->setLabel($this->_trans->trans('Submit'));
            
        // Elements.            
        $this->addElements(array($relative,$reverse,$submit));
        
        // Decorations.
        $this->_setDecoration();
    }
    
    /*********** PUBLIC FUNCTION *************/
    /**
     * Get relatives.
     * 
     * return possible relatives
     * @param $options - options
     * @return possible relatives
     */
    private function _getRelatives(array $options) {
        if($options['parent_type'] == 'articles') {
            $select = Admin_Model_DbSelect_Articles::pureSelect();
            $select->columns(array('id','name','published'))
                ->where('status > -1')
                ->order('name asc');
        }
            
        // we dont want to set up relative articles again            
        if(is_array($options['exclude']) && count($options['exclude']) > 0) {
            $select->where('id NOT IN (?)',$options['exclude']);
        }
        
        $stmt = $select->query();
        return $stmt->fetchAll();            
    }
}