<?php
/**
 * Author: Tomas Pavlatka
 * Email: tomas.pavlatka@gmail.com
 * Created: 24.1.2010 10:16:42
 */

class Admin_Form_User extends Admin_Form_AppForm {
    
    /**
     * Construct.
     * 
     * constructor of class
     * @param $options - options
     */
    public function __construct($options = null) {
        parent::__construct($options);

        // Element prefix.
        $this->addElementPrefixPath('PTX_Validate', 'PTX/Validate/',Zend_Form_Element::VALIDATE);
        
        // Translation.
        $this->_loadTranslation();
        
        // name //
        $name = new Zend_Form_Element_Text('personname');
        $name
            ->setLabel($this->_trans->trans('Person name'))
            ->setOptions(array('class'=>"long",'maxlength'=>100))
            ->addFilters(array('StripTags','StringTrim'))
            ->setRequired(true)
            ->addValidator('StringLength',false,array(1,100));
            
        // email //
        $email = new Zend_Form_Element_Text('email');
        $email
            ->setLabel($this->_trans->trans('E-mail'))
            ->setOptions(array('class'=>"long big",'maxlength'=>100))
            ->addFilters(array('StripTags','StringTrim'))
            ->addValidator('StringLength',false,array(1,100)) 
            ->addValidator('EmailAddress')
            ->setRequired(true);
        
        // password //
        $password = new Zend_Form_Element_Password('password');
        $password
            ->setLabel($this->_trans->trans('Password'))
            ->setOptions(array('class'=>"long"))
            ->addValidator('fieldCompare', false, array('password'))
            ->setRequired(true);
            
        // password confirm//
        $passwordConfirm = new Zend_Form_Element_Password('passwordconfirm');
        $passwordConfirm
            ->setLabel($this->_trans->trans('Retype password'))
            ->setOptions(array('class'=>"long"))
            ->addValidator('fieldCompare', false, array('password', 'eq'))
            ->setErrorMessages(array($this->_trans->trans('Password and Retype password must be exactly same.')))
            ->setRequired(true);
                    
        // role //
        $role = new Zend_Form_Element_Select('role');
        $role
            ->setLabel($this->_trans->trans('User role'))
            ->setOptions(array('class'=>"medium"))
            ->addMultiOption(null,$this->_trans->trans('- select user role -'))
            ->addMultiOption('users',$this->_trans->trans('Users'))
            ->addMultiOption('admins',$this->_trans->trans('Admins'))
            ->addMultiOption('sadmins',$this->_trans->trans('Super Admins'))
            ->setRequired(true);

        // locale //
        $locale = new Zend_Form_Element_Select('locale');
        $locale
            ->setLabel($this->_trans->trans('Language'))
            ->setOptions(array('class'=>"medium"))
            ->addMultiOption(null,$this->_trans->trans('- select language -'))
            ->setRequired(true);
        foreach($this->_languages as $key => $values) {
            $locale->addMultiOption($values['locale'],$values['name']);
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
            ->addMultiOption(1,$this->_trans->trans('Add another user'))
            ->addMultiOption(0,$this->_trans->trans('User detail'))
            ->setValue(1);
            
        // submit //
        $submit = new Zend_Form_Element_Submit('submit');
        $submit
            ->setOptions(array('class'=>"input-submit"))    
            ->setLabel($this->_trans->trans('Submit'));
            
        // Elements.
        if(isset($options['id'])) {
            $this->addElements(array($email,$name,$locale));
        } else {
            $this->addElements(array($email,$password,$passwordConfirm,$name,$locale,$continue));
        }
        
        // Admin elements.
        if($options['user_role'] == 'sadmins') {
            $this->addElements(array($role,$status));
        }
        
        // Submit
        $this->addElement($submit);
        
        // Decoration.
        $this->_setDecoration();
    }
}