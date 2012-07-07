<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Oct 13, 2011
 */
  
class Admin_Form_Language extends Admin_Form_AppForm {
    
    /**
     * Construct.
     * 
     * constructor of class
     * @param array $params - additional params.
     * @param array $options = options
     */
    public function __construct(array $params = array(), $options = array()) {
        parent::__construct($options);

        // Translation.
        $this->_loadTranslation();
        
        foreach($this->_languages as $key => $values) {
            // name //
            $name = new Zend_Form_Element_Text('name'.$values['code']);
            $name
                ->setLabel($this->_trans->trans('Name'))
                ->setOptions(array('class'=>"long big",'maxlength'=>255))
                ->addFilters(array('StripTags','StringTrim'))
                ->addValidator('StringLength',false,array(1,255))
                ->setRequired(true);
                
            $this->addElements(array($name));
        } 
            
        // Code.
        $code = new Zend_Form_Element_Text('code');
        $code
            ->setLabel($this->_trans->trans('Code'))
            ->setOptions(array('class'=>"small",'maxlength'=>2))
            ->addFilters(array('StripTags','StringTrim'))
            ->setRequired(true)
            ->addValidator('StringLength',false,array(2,2));
            
        // Locale.
        $locale = new Zend_Form_Element_Text('locale');
        $locale
            ->setLabel($this->_trans->trans('Locale'))
            ->setOptions(array('class'=>"small",'maxlength'=>5))
            ->addFilters(array('StripTags','StringTrim'))
            ->setRequired(true)
            ->addValidator('StringLength',false,array(5,5));
            
        // Charset.
        $charset = new Zend_Form_Element_Text('charset');
        $charset
            ->setLabel($this->_trans->trans('Charset'))
            ->setOptions(array('class'=>"small",'maxlength'=>2))
            ->addFilters(array('StripTags','StringTrim'))
            ->setValue('utf-8')
            ->setRequired(true);
            
        
        // status //
        $status = new Zend_Form_Element_Radio('status');
        $status
            ->setLabel($this->_trans->trans('Status'))
            ->addMultiOption(1,$this->_trans->trans('Active'))
            ->addMultiOption(0,$this->_trans->trans('Non-active'))
            ->setValue(1);
            
        // continue //
        $continue = new Zend_Form_Element_Radio('continue');
        $continue
            ->setLabel($this->_trans->trans('Continue'))
            ->addMultiOption(1,$this->_trans->trans('Add another banner'))
            ->addMultiOption(0,$this->_trans->trans('Banner list'))
            ->setValue(1);
                    
        // submit //
        $submit = new Zend_Form_Element_Submit('submit');
        $submit
            ->setOptions(array('class'=>"input-submit"))    
            ->setLabel($this->_trans->trans('Submit'));
            
        if(!isset($params['id'])) {            
            $this->addElements(array($code,$locale,$charset,$status,$continue,$submit));
        } else {
            $this->addElements(array($code,$locale,$charset,$status,$submit));
        }
        
        // Set decoration.
        $transFields = array('name');
        $this->_setDecoration(true,false,$transFields);
    }
}