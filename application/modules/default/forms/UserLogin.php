<?php
class Default_Form_UserLogin extends Zend_Form  {
    
	public function __construct($option = null) {
        parent::__construct($option);
        
        $this->setName('login');
        
        $email = new Zend_Form_Element_Text('email');
        $email
            ->setLabel($this->getView()->trans('E-mail address'))
            ->setOptions(array('class'=>"long"))
            ->addFilters(array('StringTrim','StripTags'))
            ->addValidator('EmailAddress')
            ->addValidator('StringLength',false,array(1,100))
            ->setRequired(true);
                 
        $password = new Zend_Form_Element_Password('password');
        $password
            ->setLabel($this->getView()->trans('Password'))
            ->setOptions(array('class'=>"long"))
            ->setRequired(true);
                 
        $login = new Zend_Form_Element_Submit('login');
        $login
            ->setLabel($this->getView()->trans('Sign In'));
        
        $this->addElements(array($email, $password, $login));
        $this->addElement('hash', 'no_csrf_foo', array('salt' => 'unique'));
        
        $this->setMethod('post');
        
        $this->setOptions(array('id'=>"loginform"));
        foreach ($this->getElements() as $el) {
            $el->addDecorator('Label',array('tag' => 'div','class'=>'formlabel','id' => 'dt_' . $el->getName()));
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
