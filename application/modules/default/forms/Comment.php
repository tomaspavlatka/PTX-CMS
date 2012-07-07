<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 9.11.2010
**/

class Default_Form_Comment extends Default_Form_AppForm {
    
    /************* PUBLIC FUNCTION ***************/
    /**
     * Construct.
     * 
     * constructor of class
     * @param $options - options     
     */
    public function __construct($options = null) {
        parent::__construct($options);

        // Translations.
        $this->_loadTranslation();
        
        // Name.
        $name = new Zend_Form_Element_Text('name');
        $name
            ->setLabel($this->_trans->trans('Person name'))
            ->setOptions(array('class'=>"long big text",'maxlength'=>150))
            ->addFilters(array('StripTags','StringTrim'))
            ->addValidator('StringLength',false,array(1,150))
            ->setRequired(true);

        // Email.
        $email = new Zend_Form_Element_Text('email');
        $email
            ->setLabel($this->_trans->trans('E-mail'))
            ->setOptions(array('class'=>"long text"))
            ->addValidator('EmailAddress')
            ->setRequired(true)
            ->setDescription($this->_trans->trans('Required, but never shared.'))
            ->addFilters(array('StripTags','StringTrim'))
            ->addValidator('StringLength',false,array(1,255));

        // Website.
        $website = new Zend_Form_Element_Text('website');
        $website
            ->setLabel($this->_trans->trans('Website'))
            ->setOptions(array('class'=>"long text"))
            ->addFilters(array('StripTags','StringTrim'))
            ->setDescription($this->_trans->trans('Start with http:// or https://'))
            ->addValidator('StringLength',false,array(1,255));
            
        // Content.
        $content = new Zend_Form_Element_Textarea('message');
        $content 
            ->setLabel($this->_trans->trans('Leave a Reply'))
            ->addFilters(array('StripTags','StringTrim'))
            ->setOptions(array('class' => "long",'rows'=>10,'cols'=>100))
            ->setRequired(true);
            
        // Parent.
        $idParent = new Zend_Form_Element_Hidden('parent');
            
        // Submit.
        $submit = new Zend_Form_Element_Submit('submit');
        $submit
            ->setOptions(array('class'=>"input-submit button",'escaped'=>false))    
            ->setLabel($this->_trans->trans('Submit comment',false));
            
        // Elements.            
        $this->addElements(array($name,$email,$website,$content,$idParent,$submit));

        // Decorations.
        $this->_setDecoration(true,false,'comment-form');
    }
}