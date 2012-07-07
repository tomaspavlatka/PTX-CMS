<?php
/**
 * Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
 * Created: Sep 23, 2011
 */
  
class Admin_Form_Banner extends Admin_Form_AppForm {
    
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
                ->setOptions(array('class'=>"long big",'maxlength'=>150))
                ->addFilters(array('StripTags','StringTrim'))
                ->addValidator('StringLength',false,array(1,150));
                
            if($key == 0) {
                $name->setRequired(true);
            }
            
            // Title.
            $title = new Zend_Form_Element_Text('title'.$values['code']);
            $title
                ->setLabel($this->_trans->trans('Title'))
                ->setOptions(array('class'=>"long",'maxlength'=>255))
                ->addFilters(array('StripTags','StringTrim'))
                ->addValidator('StringLength',false,array(1,255));
                
            // Link
            $link = new Zend_Form_Element_Text('link'.$values['code']);
            $link
                ->setLabel($this->_trans->trans('Link'))
                ->addFilters(array('StripTags','StringTrim'))
                ->setOptions(array('class' => "long",'maxlength'=>255))
                ->addValidator('StringLength',false,array(1,255));
                
            $this->addElements(array($name,$title,$link));
        } 
            
        
            
        // Parent ID.
        $parent = new Zend_Form_Element_Select('parent');
        $parent
            ->setLabel($this->_trans->trans('Section'))
            ->setOptions(array('class'=>"medium"))
            ->addMultiOption(0,$this->_trans->trans('- select section -'))
            ->setRequired(true)
            ->addMultiOptions($params['sections'])
            ->setValue($params['section']); 
            
        // Code.
        $code = new Zend_Form_Element_Textarea('code');
        $code
            ->setLabel($this->_trans->trans('Banner Code'))
            ->setOptions(array('class' => "long",'rows'=>10,'cols'=>10));
            
        // Photo.
        $photo = new Zend_Form_Element_File('logo');
        $photo
            ->setLabel($this->_trans->trans('Logo'))
            ->setDestination('./userfiles/images/banner/')
            ->addValidator('Size', false, 512000)
            ->addValidator('Extension', false, 'jpg,png,gif');
            
        // Target.
        $target = new Zend_Form_Element_Radio('target');
        $target
            ->setLabel($this->_trans->trans('Link goes to'))
            ->addMultiOption(1,$this->_trans->trans('Same window / tab'))
            ->addMultiOption(2,$this->_trans->trans('New window / tab'))
            ->setValue(1);
                        
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
            $this->addElements(array($parent,$photo,$code,$target,$status,$continue,$submit));
        } else {
            $this->addElements(array($parent,$code,$target,$status,$submit));
        }
        
        // Set decoration.
        $transFields = array('name','link','title');
        $this->_setDecoration(true,true,$transFields);
    }
}