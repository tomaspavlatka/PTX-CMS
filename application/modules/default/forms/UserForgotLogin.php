<?php
class Default_Form_UserForgotLogin extends Default_Form_AppForm  {
    
    /**
     * Construct.
     * 
     * constuctor of class
     * @param $option - options
     */
	public function __construct($option = null) {
        parent::__construct($option);
        
        // Translation.
        $this->_loadTranslation();
        $this->setName('login');
        
        $email = new Zend_Form_Element_Text('email');
        $email
            ->setLabel($this->getView()->trans('E-mail'))
            ->setOptions(array('class'=>"long"))
            ->addFilters(array('StringTrim','StripTags'))
            ->addValidator('EmailAddress')
            ->setRequired(true)
            ->addValidator('StringLength',false,array(1,100));
                 
        $submit = new Zend_Form_Element_Submit('login');
        $submit
            ->setLabel($this->getView()->trans('Remind me'));
        
        $this->addElements(array($email, $submit));
        $this->addElement('hash', 'no_csrf_foo', array('salt' => 'unique'));
        
        $this->setMethod('post');
        
        $this->setOptions(array('id'=>"loginform"));
        
        foreach ($this->getElements() as $el) {
            $el->addDecorator('Label',array('tag' => 'div','class'=>'formlabel','requiredSuffix' => ' *','id' => 'dt_' . $el->getName()));
            $el->addDecorator('Description',array('tag' => 'p','class' => 'hint','id' => 'hint_' . $el->getName()));
            $el->addDecorator('Errors',array('style' => 'color: red; font-weight: bold;')); 
            $el->addDecorator('HtmlTag',array('tag' => 'div','class'=>'forminput','id' => 'dd_'.$el->getName()));
        }   

        $this->addDecorator('FormElements')
             ->addDecorator('Fieldset')
             ->addDecorator('HtmlTag',array('tag' => 'div','class' => 'zendform'))
             ->addDecorator('Form');

        $this->setDisableLoadDefaultDecorators(false);
    }
}
