<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 8.9.2010
**/

class Admin_Form_WidgetText extends Admin_Form_AppForm {
    
    /**
     * Construct.
     * 
     * constructor of class
     * @param $options - options
     */
    public function __construct($options = null) {
        parent::__construct($options);
            
        // Translation.
        $this->_loadTranslation();
            
        foreach($this->_languages as $key => $values) {
            // Name
            $name = new Zend_Form_Element_Text('name'.$values['code']);
            $name
               ->setLabel($this->_trans->trans('Name'))
               ->setOptions(array('class'=>"long big",'maxlength'=>50))
               ->addFilters(array('StripTags','StringTrim'))
               ->addValidator('StringLength',false,array(1,50));
                
            // link //
            $content = new Zend_Form_Element_Textarea('wcontent'.$values['code']);
            $content
                ->setLabel($this->_trans->trans('Content'))
                ->setOptions(array('class'=>"long",'rows'=>25,'cols'=>10,'style'=>'width:920px'))
                ->setRequired(true);
                                           
            if($key == 0) {
                $name->setRequired(true);
                $content->setRequired(true);
            }
                
            $this->addElements(array($name,$content));
        } 
        
        // showname //
        $showName = new Zend_Form_Element_Radio('showname');
        $showName
            ->setLabel($this->_trans->trans('Show name'))
            ->addMultiOption(1,$this->_trans->trans('Yes'))
            ->addMultiOption(0,$this->_trans->trans('No'))
            ->setSeparator('&nbsp;')
            ->setRequired(true)
            ->setValue(0);
                        
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
            
        $this->addElements(array($showName,$status,$submit));
        
        // Decorations.
        $transFields = array('name','wcontent');
        $this->_setDecoration(false,false,$transFields); 
    }
}