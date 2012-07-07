<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 23.8.2010
**/

class Admin_Form_Category extends Admin_Form_AppForm {
    
    /**
     * Construct.
     * 
     * constructor of class
     * @param array $params - additional parameters
     * @param array $options - options
     */
    public function __construct(array $params = array(), array $options = array()) {
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
                ->addValidator('StringLength',false,array(1,255));
                
            $perex = new Zend_Form_Element_Text('perex'.$values['code']);
            $perex
                ->setLabel($this->_trans->trans('Perex'))
                ->setOptions(array('class'=>"long"))
                ->addFilters(array('StripTags','StringTrim'));
                
            // seo description //
            $description = new Zend_Form_Element_Text('seodescription'.$values['code']);
            $description
                ->setLabel($this->_trans->trans('Description [SEO]'))
                ->setOptions(array('class'=>"long",'maxlength'=>255))
                ->addFilters(array('StripTags','StringTrim'))
                ->addValidator('StringLength',false,array(1,255));
                
            // seo keywords //
            $keywords = new Zend_Form_Element_Text('seokeywords'.$values['code']);
            $keywords
                ->setLabel($this->_trans->trans('Keywords [SEO]'))
                ->setOptions(array('class'=>"long",'maxlength'=>255))
                ->addFilters(array('StripTags','StringTrim'))
                ->addValidator('StringLength',false,array(1,255));
                
            if($key == 0) {
                $name->setRequired(true);
            }
                
            $this->addElements(array($name,$perex,$description,$keywords));
        } 
        
        // parent //
        $parent = new Zend_Form_Element_Select('parent');
        $parent
            ->setLabel($this->_trans->trans('Parent'))
            ->setOptions(array('class'=>"medium"))
            ->addMultiOption(0,$this->_trans->trans('- top category -'))
            ->addMultiOptions($params['categories'])
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
            ->addMultiOption(1,$this->_trans->trans('Add another category'))
            ->addMultiOption(0,$this->_trans->trans('Category detail'))
            ->setValue(1);
                    
        // submit //
        $submit = new Zend_Form_Element_Submit('submit');
        $submit
            ->setOptions(array('class'=>"input-submit"))    
            ->setLabel($this->_trans->trans('Submit'));
            
        if(!isset($options['id'])) {            
            $this->addElements(array($parent,$status,$continue,$submit));
        } else {
            $this->addElements(array($parent,$status,$submit));
        }
        
        // Set decoration.
        $transFields = array('name','perex','seodescription','seokeywords');
        $this->_setDecoration(true,true,$transFields);
    }
}