<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Oct 6, 2011
 */
  
class Admin_Form_Replacer extends Admin_Form_AppForm {
    
    /**
     * Construct.
     * 
     * constructor of class
     * @param array $params - additional params
     * @param array $options - options
     */
    public function __construct($params = array(), $options = null) {
        parent::__construct($options);

        // Translation.
        $this->_loadTranslation();
        
        foreach($this->_languages as $key => $values) {
            // name //
            $content = new Zend_Form_Element_Textarea('pcontent'.$values['code']);
            $content
                ->setLabel($this->_trans->trans('Content'))
                ->setOptions(array('class'=>"long ptxmark",'rows'=>30,'cols'=>50,'style'=>'width:920px'));
                
            if($key == 0) {
                $content->setRequired(true);
            }
                
            $this->addElements(array($content));
        } 
        
        // Code.
        $code = new Zend_Form_Element_Text('code');
        $code
            ->setLabel($this->_trans->trans('Code'))
            ->setOptions(array('class'=>"long big",'maxlength'=>20))
            ->addFilters(array('StripTags','StringTrim'))
            ->addValidator('StringLength',false,array(1,20))
            ->setRequired(true);
            
        // Script.
        $script = new Zend_Form_Element_Text('script');
        $script
            ->setLabel($this->_trans->trans('Script'))
            ->setOptions(array('class'=>"long"))
            ->addFilters(array('StripTags','StringTrim'));

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
            ->addMultiOption(1,$this->_trans->trans('Add another replacer'))
            ->addMultiOption(0,$this->_trans->trans('Replacers list'))
            ->setValue(1);
                    
        // submit //
        $submit = new Zend_Form_Element_Submit('submit');
        $submit
            ->setOptions(array('class'=>"input-submit"))    
            ->setLabel($this->_trans->trans('Submit'));

        // Elements.
        if(!isset($params['id'])) {
            $this->addElements(array($code,$script,$status,$continue,$submit));
        } else {
        	$this->addElements(array($code,$script,$status,$submit));
        }
        
        // Set decoration.
        $transFields = array('pcontent');
        $this->_setDecoration(true,false,$transFields);
    }
}