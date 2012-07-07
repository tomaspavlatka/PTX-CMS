<?php
/**
 * Author: Tomas Pavlatka [tomas@pavlatka.cz]
 * Created: Oct 15, 2011
 */
 
class Admin_Form_SettingEmail extends Admin_Form_AppForm {
    
    /**
     * Construct.
     * 
     * constructor of class
     * @param $options - options
     */
    public function __construct($options = array()) {
        parent::__construct($options);
        
        // Translations.
        $this->_loadTranslation();
        
        foreach($this->_languages as $key => $values) {
            // Subject starts.
            $subject = new Zend_Form_Element_Text('emailsubject'.$values['code']);
            $subject
                ->setLabel($this->_trans->trans('Subject starts'))
                ->setOptions(array('class'=>"long"))
                ->addFilters(array('StripTags','StringTrim'))
                ->addValidator('StringLength',false,array(1,20));
                
            $this->addElements(array($subject));
        } 
        
        $smtpHost = new Zend_Form_Element_Text('smtphost');
        $smtpHost
            ->setLabel($this->_trans->trans('SMTP Host'))
            ->setOptions(array('class'=>"long"))
            ->addFilters(array('StripTags','StringTrim'))
            ->setValue('smtp.googlemail.com')
            ->addValidator('StringLength',false,array(1,100));
            
        $smtpAuth = new Zend_Form_Element_Text('smtpauth');
        $smtpAuth
            ->setLabel($this->_trans->trans('SMTP Auth'))
            ->setOptions(array('class'=>"long"))
            ->addFilters(array('StripTags','StringTrim'))
            ->setValue('login')
            ->addValidator('StringLength',false,array(1,100));
        
        // SMTP Email
        $smtpEmail = new Zend_Form_Element_Text('smtpemail');
        $smtpEmail
            ->setLabel($this->_trans->trans('SMTP Email'))
            ->setOptions(array('class'=>"long"))
            ->addFilters(array('StripTags','StringTrim'))
            ->addValidator('EmailAddress')
            ->addValidator('StringLength',false,array(1,100));
            
        // SMTP Password
        $smtpPassword = new Zend_Form_Element_Text('smtppasswd');
        $smtpPassword
            ->setLabel($this->_trans->trans('SMTP Password'))
            ->setOptions(array('class'=>"long"))
            ->addValidator('StringLength',false,array(1,100));

        // SMTP SSL
        $smtpSSL = new Zend_Form_Element_Text('smtpssl');
        $smtpSSL
            ->setLabel($this->_trans->trans('SMTP SSL'))
            ->setOptions(array('class'=>"long"))
            ->addFilters(array('StripTags','StringTrim'))
            ->setValue('ssl')
            ->addValidator('StringLength',false,array(1,100));
            
        // SMTP Port
        $smtpPort = new Zend_Form_Element_Text('smtpport');
        $smtpPort
            ->setLabel($this->_trans->trans('SMTP Port'))
            ->setOptions(array('class'=>"long"))
            ->addFilters(array('StripTags','StringTrim'))
            ->setValue(465)
            ->addValidator('StringLength',false,array(1,100));
            
        // SMTP Use
        $smtpuse = new Zend_Form_Element_Radio('smtpuse');
        $smtpuse
            ->setLabel($this->_trans->trans('Sent email via SMTP'))
            ->addMultiOption(1,$this->_trans->trans('Yes'))
            ->addMultiOption(0,$this->_trans->trans('No'))
            ->setValue(1);
            
        // Subject starts.
        $emailaddress = new Zend_Form_Element_Text('emailaddress');
        $emailaddress
            ->setLabel($this->_trans->trans('E-mail address'))
            ->setOptions(array('class'=>"long"))
            ->addFilters(array('StripTags','StringTrim'))
            ->setRequired(true)
            ->addValidator('StringLength',false,array(1,255));
            
        // submit //
        $submit = new Zend_Form_Element_Submit('submit');
        $submit
            ->setOptions(array('class'=>"input-submit"))    
            ->setLabel($this->_trans->trans('Submit'));
            
        $this->addElements(array($emailaddress,$smtpPassword,$smtpAuth,$smtpHost,$smtpPort,$smtpSSL,$smtpuse,$submit));
        
        // Set decoration.
        $transFields = array('emailsubject');
        $this->_setDecoration(true,false,$transFields);
    }
}