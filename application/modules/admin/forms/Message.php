<?php
/**
* Author: Tomas Pavlatka [tomas.pavlatka@gmail.com]
* Date: 25.7.2010
**/

class Admin_Form_Message extends Admin_Form_AppForm {
    
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
        
        // to //
        $to = new Zend_Form_Element_Select('to');
        $to
            ->setLabel($this->_trans->trans('To'))
            ->addMultiOption(null,$this->_trans->trans('- select user -'))
            ->setRequired(true);
            
        $users = $this->_getUsers();
        foreach($users as $row) {
            if($row['id'] != $options['id_user']) {
                $name = $row['name'] .' ('.$row['email'].')';
                $to->addMultiOption($row['id'],$name);
            }
        }
            
        // name //
        $subject = new Zend_Form_Element_Text('subject');
        $subject
            ->setLabel($this->_trans->trans('Subject'))
            ->setOptions(array('class'=>"long",'maxlenght'=>100))
            ->addFilters(array('StripTags','StringTrim'))
            ->setRequired(true)
            ->addValidator('StringLength',false,array(1,100));
            
        // address //
        $text = new Zend_Form_Element_Textarea('message');
        $text
            ->setLabel($this->_trans->trans('Message'))
            ->setOptions(array('class'=>"long",'rows'=>10, 'cols'=>10))
            ->addFilters(array('StripTags','StringTrim'))
            ->setRequired(true);
            
        // continue //
        $continue = new Zend_Form_Element_Radio('continue');
        $continue
            ->setLabel($this->_trans->trans('Continue'))
            ->addMultiOption(1,$this->_trans->trans('Send another message'))
            ->addMultiOption(0,$this->_trans->trans('Message detail'))
            ->setValue(1);
            
        // submit //
        $submit = new Zend_Form_Element_Submit('submit');
        $submit
            ->setOptions(array('class'=>"input-submit"))    
            ->setLabel($this->_trans->trans('Submit'));
            
        // Elements.                        
        if(!isset($options['id'])) {
            $this->addElements(array($to,$subject,$text,$continue,$submit));
        } else {
            $this->addElements(array($to,$subject,$text,$submit));
        }
        
        // Decorations.
        $this->_setDecoration();
    }
    
    /************ PRIVATE FUNCTION *************/
    /**
     * Get users.
     * 
     * returns all users
     * @return users
     */
    private function _getUsers() {
        $select = Admin_Model_DbSelect_Users::pureSelect();
        $select->columns(array('id','name','email'));
        $select->where('status > -1')->order('name asc');
        
        $stmt = $select->query();
        return $stmt->fetchAll();
    } 
}