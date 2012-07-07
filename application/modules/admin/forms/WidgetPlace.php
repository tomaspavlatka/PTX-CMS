<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 22.8.2010
**/

class Admin_Form_WidgetPlace extends Admin_Form_AppForm {
    
    /**
     * Construct.
     * 
     * constructor of class
     * @param $options
     */
    public function __construct($options = null) {
        parent::__construct($options);

        // Translations.
        $this->_loadTranslation();
        
        foreach($this->_languages as $key => $values) {
            // name //
            $name = new Zend_Form_Element_Text('name'.$values['code']);
            $name
                ->setLabel($this->_trans->trans('Name'))
                ->setOptions(array('class'=>"long",'maxlength'=>50))
                ->addFilters(array('StripTags','StringTrim'))
                ->addValidator('StringLength',false,array(1,50));
                
            if($key == 0) {
                $name->setRequired(true);
            }
                
            $this->addElements(array($name));
        } 
            
        // parent type //
        $parentType = new Zend_Form_Element_Select('type');
        $parentType
            ->setLabel($this->_trans->trans('Type'))
            ->setOptions(array('class'=>"medium"))
            ->setRequired(true)
            ->addMultiOption(null,$this->_trans->trans('- select type -'))
            ->addMultiOption('frontend',$this->_trans->trans('Frontend'))
            ->addMultiOption('admin',$this->_trans->trans('Backend'));
            
        // continue //
        $continue = new Zend_Form_Element_Radio('continue');
        $continue
            ->setLabel($this->_trans->trans('Continue'))
            ->addMultiOption(1,$this->_trans->trans('Add another widget place'))
            ->addMultiOption(0,$this->_trans->trans('List with widget places'))
            ->setValue(1);
            
        // status //
        $status = new Zend_Form_Element_Radio('status');
        $status
            ->setLabel($this->_trans->trans('Status'))
            ->addMultiOption(1,$this->_trans->trans('Active'))
            ->addMultiOption(0,$this->_trans->trans('Non-active'))
            ->setValue(1);
            
        // submit //
        $submit = new Zend_Form_Element_Submit('submit');
        $submit
            ->setOptions(array('class'=>"input-submit"))    
            ->setLabel($this->_trans->trans('Submit'));
            
        if(!isset($options['id'])) {
            $this->addElements(array($parentType,$status,$continue,$submit));
        } else {
            $this->addElements(array($parentType,$status,$submit));
        }
        
        // Set decoration.
        $transFields = array('name');
        $this->_setDecoration(true,false,$transFields);
    }
}