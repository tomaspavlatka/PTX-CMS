<?php
class Admin_Form_Setting extends Admin_Form_AppForm {
    
    /**
     * Construct.
     * 
     * constructor of class
     * @param $options - options
     */
    public function __construct($options = array()) {
        parent::__construct($options);
        
        // Translations.
        $this->_loadTranslation();
        
        foreach($this->_languages as $key => $values) {
            // Name
            $name = new Zend_Form_Element_Text('name'.$values['code']);
            $name
                ->setLabel($this->_trans->trans('Project'))
                ->setOptions(array('class'=>"long big",'maxlength'=>100))
                ->addFilters(array('StripTags','StringTrim'))
                ->addValidator('StringLength',false,array(1,100));
                
            // Slogan
            $slogan = new Zend_Form_Element_Text('slogan'.$values['code']);
            $slogan
                ->setLabel($this->_trans->trans('Page slogan'))
                ->setOptions(array('class'=>"long",'maxlength'=>150))
                ->addFilters(array('StripTags','StringTrim'))
                ->addValidator('StringLength',false,array(1,150));
                
            if($key == 0) {
                $name->setRequired(true);
            }
                
            $this->addElements(array($name,$slogan));
        } 
        
        // google api //
        $gooleVerify = new Zend_Form_Element_Text('googleverify');
        $gooleVerify
            ->setLabel($this->_trans->trans('Google verification key'))
            ->setOptions(array('class'=>"long"))
            ->addFilters(array('StripTags','StringTrim'))
            ->addValidator('StringLength',false,array(1,100));
            
        // Picasa ID
        $picasaId = new Zend_Form_Element_Text('picasaid');
        $picasaId
            ->setLabel($this->_trans->trans('Picasa Id'))
            ->setOptions(array('class'=>"long"))
            ->addFilters(array('StripTags','StringTrim'))
            ->addValidator('StringLength',false,array(1,100));
            
        // submit //
        $submit = new Zend_Form_Element_Submit('submit');
        $submit
            ->setOptions(array('class'=>"input-submit"))    
            ->setLabel($this->_trans->trans('Submit'));
            
        $this->addElements(array($gooleVerify,$picasaId,$submit));
        
        // Set decoration.
        $transFields = array('name','slogan');
        $this->_setDecoration(true,false,$transFields);
    }
}