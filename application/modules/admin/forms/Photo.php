<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: Dec 20, 2010
**/

class Admin_Form_Photo extends Admin_Form_AppForm {
    
    /**
     * Construct.
     * 
     * constructor of class
     * @param array $params - additional params
     * @param array $options = options
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

            // SEO Description //
            $description = new Zend_Form_Element_Text('seodescription'.$values['code']);
            $description
                ->setLabel($this->_trans->trans('Description [SEO]'))
                ->addFilters(array('StripTags','StringTrim'))
                ->setOptions(array('class' => "long",'maxlength'=>255))
                ->addValidator('StringLength',false,array(1,255));

            // SEO Keywords //
            $keywords = new Zend_Form_Element_Text('seokeywords'.$values['code']);
            $keywords
                ->setLabel($this->_trans->trans('Keywords [SEO]'))
                ->addFilters(array('StripTags','StringTrim'))
                ->setOptions(array('class' => "long",'maxlength'=>255))
                ->addValidator('StringLength',false,array(1,255));
                
            if($key == 0) {
                $name->setRequired(true);
            }
                
            $this->addElements(array($name,$perex,$description,$keywords));
        } 
            
        // Photo.
        $photo = new Zend_Form_Element_File('logo');
        $photo
            ->setLabel($this->_trans->trans('Photo'))
            ->setDestination('./userfiles/images/photo/')
            ->setRequired(true)                
            ->addValidator('Size', false, 512000)
            ->addValidator('Extension', false, 'jpg,png,gif');
            
        // Parent.
        $parent = new Zend_Form_Element_Select('parent');
        $parent
            ->setLabel($this->_trans->trans('Category'))
            ->setOptions(array('class'=>"long"))
            ->setValue($params['parent'])
            ->addMultiOption(null,$this->_trans->trans('- select category -'))
            ->addMultiOptions($params['categories'])
            ->setRequired(true);        

        // Tags.
        $tags = new Zend_Form_Element_MultiCheckbox('tags');
        $tags
            ->setOptions(array('class'=>"checkbox-contatiner"))
            ->setSeparator('&nbsp;') 
            ->setLabel($this->_trans->trans('Tags'));
            
        $dbTags = $this->_getTags();
        foreach($dbTags as $tag) {
            $tags->addMultiOption($tag['id'],$tag['name']);
        }
                        
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
            ->addMultiOption(1,$this->_trans->trans('Add another photo'))
            ->addMultiOption(0,$this->_trans->trans('Photo list'))
            ->setValue(1);
                    
        // submit //
        $submit = new Zend_Form_Element_Submit('submit');
        $submit
            ->setOptions(array('class'=>"input-submit"))    
            ->setLabel($this->_trans->trans('Submit'));
            
        if(!isset($params['id'])) {            
            $this->addElements(array($photo,$parent,$status,$continue,$tags,$submit));
        } else {
            $this->addElements(array($parent,$status,$tags,$submit));
        }
        
        // Set decoration.
        $transFields = array('name','perex','seodescription','seokeywords');
        $this->_setDecoration(true,true,$transFields);
    }
}