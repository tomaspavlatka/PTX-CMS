<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 22.8.2010
**/

class Admin_Form_StaticPage extends Admin_Form_AppForm {
    
    /**
     * Construct.
     * 
     * constructor of class
     * @param $options - options
     */
    public function __construct($options = null) {
        parent::__construct($options);

        // Load translation.
        $this->_loadTranslation();
        
        foreach($this->_languages as $key => $values) {
            // name //
            $name = new Zend_Form_Element_Text('name'.$values['code']);
            $name
	            ->setLabel($this->_trans->trans('Name'))
	            ->setOptions(array('class'=>"long big",'maxlength'=>255))
	            ->addFilters(array('StripTags','StringTrim'))
	            ->addValidator('StringLength',false,array(1,255));
	            
            // Content.
            $content = new Zend_Form_Element_Textarea('pcontent'.$values['code']);
            $content
                ->setLabel($this->_trans->trans('Content'))
                ->setOptions(array('class'=>"long",'rows'=>30,'cols'=>50));
	            
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
	            
	        // notice //
            $notice = new Zend_Form_Element_Textarea('notice'.$values['code']);
            $notice
                ->setLabel($this->_trans->trans('Revision notice'))
                ->setOptions(array('class'=>"long",'rows'=>3,'cols'=>50));
                
            if($key == 0) {
                $name->setRequired(true);
                $content->setRequired(true);
            }
                
            $this->addElements(array($name,$content,$description,$keywords,$notice));
        } 

        // published //
        $published = new Zend_Form_Element_Text('published');
        $published
            ->setLabel($this->_trans->trans('Published'))
            ->setOptions(array('class'=>"middle",'maxlength'=>30))
            ->addFilters(array('StripTags','StringTrim'))
            ->addValidator('Regex',false,array('/^([0][1-9]|[1-2][0-9]|[3][0-1])\/([0][1-9]|[1][0-2])\/[0-9]{4} ([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/'))
            ->setErrorMessages(array($this->getView()->trans('Publihed must meet dd/mm/yyyy hh:ii:ss patern')))
            ->setRequired(true);
            
        // status //
        $status = new Zend_Form_Element_Radio('status');
        $status
            ->setLabel($this->_trans->trans('Status'))
            ->addMultiOption(1,$this->getView()->trans('Active'))
            ->addMultiOption(0,$this->getView()->trans('Non-active'))
            ->setValue(1);
                    
        // submit //
        $submit = new Zend_Form_Element_Submit('submit');
        $submit
            ->setOptions(array('class'=>"input-submit"))    
            ->setLabel($this->_trans->trans('Submit'));
            
        $this->addElements(array($published,$status,$submit));
        
        // Set decoration.
        $transFields = array('name','pcontent','seodescription','seokeywords','notice');
        $this->_setDecoration(true,false,$transFields);
    }
}