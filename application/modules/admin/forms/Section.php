<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Sep 23, 2011
 */
  
class Admin_Form_Section extends Admin_Form_AppForm {
    
    /**
     * Construct.
     * 
     * constructor of class
     * @param $options = options
     */
    public function __construct($options = array()) {
        parent::__construct($options);

        // Translation.
        $this->_loadTranslation();
            
        foreach($this->_languages as $key => $values) {
            // name //
            $name = new Zend_Form_Element_Text('name'.$values['code']);
            $name
                ->setLabel($this->_trans->trans('Name'))
                ->setOptions(array('class'=>"long big",'maxlength'=>150))
                ->addFilters(array('StripTags','StringTrim'))
                ->addValidator('StringLength',false,array(1,150));
                
            if($key == 0) {
                $name->setRequired(true);
            }
                
            $this->addElements(array($name));
        } 
                        
        // Status.
        $status = new Zend_Form_Element_Radio('status');
        $status
            ->setLabel($this->_trans->trans('Status'))
            ->addMultiOption(1,$this->_trans->trans('Active'))
            ->addMultiOption(0,$this->_trans->trans('Non-active'))
            ->setValue(1);
            
        // Continue.
        $continue = new Zend_Form_Element_Radio('continue');
        $continue
            ->setLabel($this->_trans->trans('Continue'))
            ->addMultiOption(1,$this->_trans->trans('Add another section'))
            ->addMultiOption(0,$this->_trans->trans('Sections list'))
            ->setValue(1);
                    
        // submit //
        $submit = new Zend_Form_Element_Submit('submit');
        $submit
            ->setOptions(array('class'=>"input-submit"))    
            ->setLabel($this->_trans->trans('Submit'));
            
        if(!isset($options['id'])) {            
            $this->addElements(array($status,$continue,$submit));
        } else {
            $this->addElements(array($status,$submit));
        }
        
        // Set decoration.
        $transFields = array('name');
        $this->_setDecoration(true,false,$transFields);
    }
}