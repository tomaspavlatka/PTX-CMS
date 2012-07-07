<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 25.7.2010
**/

class Admin_Form_UserPasswd extends Admin_Form_AppForm {
    
    /**
     * Construct.
     * 
     * constructor of class
     * @param $options
     */
    public function __construct($options = null) {
        parent::__construct($options);
        
        // Element prefix.
        $this->addElementPrefixPath('PTX_Validate', 'PTX/Validate/',Zend_Form_Element::VALIDATE);
        
        // Translation.
        $this->_loadTranslation();
        
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
            
        // submit //
        $submit = new Zend_Form_Element_Submit('submit');
        $submit
            ->setOptions(array('class'=>"input-submit"))    
            ->setLabel($this->_trans->trans('Submit'));
            
        $this->addElements(array($password,$passwordConfirm,$submit));
        
        // Decoration.
        $this->_setDecoration();
    }
}