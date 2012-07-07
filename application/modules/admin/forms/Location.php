<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Oct 22, 2011
 */
  
class Admin_Form_Location extends Admin_Form_AppForm {
    
    /**
     * Construct.
     * 
     * constructor of class
     * @param array $params - additional parameters
     * @param array $options - options
     */
    public function __construct(array $params,$options = array()) {
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
        
        // Parent.
        $parent = new Zend_Form_Element_Select('parent');
        $parent
            ->setLabel($this->_trans->trans('Parent'))
            ->setOptions(array('class'=>"medium"))
            ->addMultiOption(0,$this->_trans->trans('- top level -'))
            ->addMultiOptions($params['parents'])
            ->setRequired(true);        
                        
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
            ->addMultiOption(1,$this->_trans->trans('Add another location'))
            ->addMultiOption(0,$this->_trans->trans('Locations list'))
            ->setValue(1);
                    
        // submit //
        $submit = new Zend_Form_Element_Submit('submit');
        $submit
            ->setOptions(array('class'=>"input-submit"))    
            ->setLabel($this->_trans->trans('Submit'));
            
        if(!isset($params['id'])) {            
            $this->addElements(array($parent,$status,$continue,$submit));
        } else {
            $this->addElements(array($parent,$status,$submit));
        }
        
        // Set decoration.
        $transFields = array('name');
        $this->_setDecoration(true,false,$transFields);
    }
}