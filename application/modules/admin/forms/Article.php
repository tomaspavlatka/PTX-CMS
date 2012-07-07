<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 23.8.2010
**/

class Admin_Form_Article extends Admin_Form_AppForm {
    
    /**
     * Construct.
     * 
     * constructor of class
     * @param array $params - additional params
     * @param array $options - options
     */
    public function __construct(array $params = array(), array $options = null) {
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
                
            // name //
            $shortname = new Zend_Form_Element_Text('shortname'.$values['code']);
            $shortname
                ->setLabel($this->_trans->trans('Short Name'))
                ->setOptions(array('class'=>"long",'maxlength'=>100))
                ->addFilters(array('StripTags','StringTrim'))
                ->addValidator('StringLength',false,array(1,100));
                                
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
                
            // Content.
            $content = new Zend_Form_Element_Textarea('pcontent'.$values['code']);
            $content
                ->setLabel($this->_trans->trans('Content'))
                ->setOptions(array('class'=>"long",'rows'=>30,'cols'=>50));
                
            
            // Perex.
            $perex = new Zend_Form_Element_Textarea('perex'.$values['code']);
            $perex
                ->setLabel($this->_trans->trans('Perex'))
                ->setOptions(array('class'=>"long ptxmark",'rows'=>30,'cols'=>50))
                ->setRequired(false);
                
            // Notice.
            $notice = new Zend_Form_Element_Textarea('notice'.$values['code']);
            $notice
                ->setLabel($this->_trans->trans('Revision notice'))
                ->setOptions(array('class'=>"long",'rows'=>5,'cols'=>50));
                
            if($key == 0) {
                $name->setRequired(true);
                $perex->setRequired(true);
                $content->setRequired(true);
            }
                
            $this->addElements(array($name,$shortname,$description,$keywords,$content,$perex,$notice));
        } 
            
        // published //
        $published = new Zend_Form_Element_Text('published');
        $published
            ->setLabel($this->_trans->trans('Published'))
            ->setOptions(array('class'=>"middle",'maxlength'=>30))
            ->addFilters(array('StripTags','StringTrim'))
            ->addValidator('Regex',false,array('/^([0][1-9]|[1-2][0-9]|[3][0-1])\/([0][1-9]|[1][0-2])\/[0-9]{4} ([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/'))
            ->setErrorMessages(array($this->_trans->trans('Publihed must meet dd/mm/yyyy hh:ii:ss')))
            ->setRequired(true);
            
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

        // Elements.
        $this->addElements(array($published,$status,$submit));
        
         // Set decoration.
        $transFields = array('name','shortname','perex','pcontent','seodescription','seokeywords','notice');
        $this->_setDecoration(true,false,$transFields);
    }
}