<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 21.10.2010
**/

class Admin_Form_WidgetSeparator extends Admin_Form_AppForm {
    
    /**
     * Construct.
     * 
     * constructor of class
     * @param array $options - options
     */
    public function __construct($options = null) {
        parent::__construct($options);

        // Translations.
        $this->_loadTranslation();

        foreach($this->_languages as $key => $values) {
            // Name
            $name = new Zend_Form_Element_Text('name'.$values['code']);
            $name
               ->setLabel($this->_trans->trans('Name'))
               ->setOptions(array('class'=>"long big",'maxlength'=>50))
               ->addFilters(array('StripTags','StringTrim'))
               ->addValidator('StringLength',false,array(1,50));
                                           
            if($key == 0) {
                $name->setRequired(true);
            }
                
            $this->addElements(array($name));
        } 
        
        // status //
        $status = new Zend_Form_Element_Radio('status');
        $status
            ->setLabel($this->_trans->trans('Status'))
            ->addMultiOption(1,$this->_trans->trans('Active'))
            ->addMultiOption(0,$this->_trans->trans('Non-active'))
            ->setSeparator('&nbsp;')
            ->setRequired(true)
            ->setValue(1);
            
        // submit //
        $submit = new Zend_Form_Element_Submit('submit');
        $submit
            ->setOptions(array('class'=>"input-submit"))    
            ->setLabel($this->_trans->trans('Submit'));
            
        $this->addElements(array($status,$submit));
        
        // Decorations.
        $transFields = array('name');
        $this->_setDecoration(true,false,$transFields); 
    }
}